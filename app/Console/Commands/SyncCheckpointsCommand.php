<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Checkpoint;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sync local data to VPS via HTTPS POST API.
 *
 * Sends unsynced checkpoints, all users, and all employees
 * to the VPS endpoint in batches. On success, marks checkpoints
 * as synced (sync=1) in the local database.
 *
 * Usage:
 *   php artisan sync:checkpoints
 *   php artisan sync:checkpoints --force   (re-sync all checkpoints)
 *
 * Scheduled via sync_checkpoints.bat or Task Scheduler.
 */
class SyncCheckpointsCommand extends Command
{
    protected $signature = 'sync:checkpoints {--force : Re-sync all checkpoints regardless of sync status}';

    protected $description = 'Sync checkpoints, users, and employees data to VPS via API';

    /**
     * Number of records per HTTP request batch.
     */
    private const CHUNK_SIZE = 50;

    /**
     * HTTP request timeout in seconds.
     */
    private const TIMEOUT = 30;

    public function handle()
    {
        $baseUrl = config('services.vps_sync.url');
        $token = config('services.vps_sync.token');

        if (empty($baseUrl) || empty($token)) {
            $this->error('VPS_SYNC_URL atau VPS_SYNC_TOKEN belum dikonfigurasi di .env');
            return 1;
        }

        $this->info('=== Starting VPS Sync ===');
        $this->newLine();

        // 1. Sync employees first (no FK dependencies)
        $this->syncEmployees($baseUrl, $token);

        // 2. Sync users (depends on employees via employee_id)
        $this->syncUsers($baseUrl, $token);

        // 3. Sync checkpoints (depends on users via created_by, started_by, etc.)
        $this->syncCheckpoints($baseUrl, $token);

        $this->newLine();
        $this->info('=== VPS Sync Completed ===');

        return 0;
    }

    /**
     * Sync all employees to VPS.
     */
    private function syncEmployees(string $baseUrl, string $token): void
    {
        $this->info('[Employees] Fetching data...');

        $employees = Employee::all();

        if ($employees->isEmpty()) {
            $this->info('[Employees] No data to sync.');
            return;
        }

        $this->info("[Employees] Sending {$employees->count()} records...");

        $totalSynced = 0;
        $totalFailed = 0;

        foreach ($employees->chunk(self::CHUNK_SIZE) as $chunk) {
            $payload = $chunk->map(function ($employee) {
                return $employee->getAttributes();
            })->values()->toArray();

            $result = $this->sendToVps("{$baseUrl}/employees", $token, ['employees' => $payload]);

            if ($result) {
                $totalSynced += $result['synced_count'] ?? 0;
                $totalFailed += $result['failed_count'] ?? 0;
            } else {
                $totalFailed += count($payload);
            }
        }

        $this->info("[Employees] Done. Synced: {$totalSynced}, Failed: {$totalFailed}");
    }

    /**
     * Sync all users to VPS.
     */
    private function syncUsers(string $baseUrl, string $token): void
    {
        $this->info('[Users] Fetching data...');

        $users = User::all();

        if ($users->isEmpty()) {
            $this->info('[Users] No data to sync.');
            return;
        }

        $this->info("[Users] Sending {$users->count()} records...");

        $totalSynced = 0;
        $totalFailed = 0;

        foreach ($users->chunk(self::CHUNK_SIZE) as $chunk) {
            $payload = $chunk->map(function ($user) {
                $attrs = $user->getAttributes();
                // Remove sensitive fields not needed on VPS
                unset($attrs['remember_token']);
                return $attrs;
            })->values()->toArray();

            $result = $this->sendToVps("{$baseUrl}/users", $token, ['users' => $payload]);

            if ($result) {
                $totalSynced += $result['synced_count'] ?? 0;
                $totalFailed += $result['failed_count'] ?? 0;
            } else {
                $totalFailed += count($payload);
            }
        }

        $this->info("[Users] Done. Synced: {$totalSynced}, Failed: {$totalFailed}");
    }

    /**
     * Sync unsynced checkpoints to VPS.
     */
    private function syncCheckpoints(string $baseUrl, string $token): void
    {
        $this->info('[Checkpoints] Fetching unsynced data...');

        $query = Checkpoint::query();

        if ($this->option('force')) {
            $this->warn('[Checkpoints] --force flag: re-syncing ALL checkpoints.');
        } else {
            $query->where('sync', 0);
        }

        $unsynced = $query->get();

        if ($unsynced->isEmpty()) {
            $this->info('[Checkpoints] No data to sync.');
            return;
        }

        $this->info("[Checkpoints] Sending {$unsynced->count()} records...");

        $totalSynced = 0;
        $totalFailed = 0;
        $batchNumber = 0;

        foreach ($unsynced->chunk(self::CHUNK_SIZE) as $chunk) {
            $batchNumber++;
            $payload = $chunk->map(function ($checkpoint) {
                return $checkpoint->getAttributes();
            })->values()->toArray();

            $result = $this->sendToVps("{$baseUrl}/checkpoints", $token, ['checkpoints' => $payload]);

            if ($result === null) {
                // Total HTTP failure — already logged by sendToVps
                $totalFailed += count($payload);
                $this->warn("[Checkpoints] Batch #{$batchNumber}: HTTP request gagal total ({$totalFailed} records).");
                continue;
            }

            // Sync successful IDs
            $syncedIds = $result['synced_ids'] ?? [];
            $failedItems = $result['failed'] ?? [];

            if (!empty($syncedIds)) {
                Checkpoint::whereIn('id', $syncedIds)->update(['sync' => 1]);
            }

            $totalSynced += count($syncedIds);
            $totalFailed += count($failedItems);

            // Show individual failures with detail
            foreach ($failedItems as $failed) {
                $this->warn("[Checkpoints] Failed ID {$failed['id']}: {$failed['error']}");
            }
        }

        $this->info("[Checkpoints] Done. Synced: {$totalSynced}, Failed: {$totalFailed}");
    }

    /**
     * Send data to VPS via HTTPS POST.
     *
     * @param  string  $url     Full API endpoint URL
     * @param  string  $token   Bearer token
     * @param  array   $data    Request payload
     * @return array|null       Decoded response on success, null on failure
     */
    private function sendToVps(string $url, string $token, array $data): ?array
    {
        try {
            $response = Http::withToken($token)
                ->withoutVerifying()
                ->timeout(self::TIMEOUT)
                ->retry(3, 1000, function ($exception) {
                    // Retry on connection errors and 5xx responses
                    return $exception instanceof \Illuminate\Http\Client\ConnectionException
                        || ($exception instanceof \Illuminate\Http\Client\RequestException
                            && $exception->response?->status() >= 500);
                })
                ->post($url, $data);

            if ($response->successful()) {
                return $response->json();
            }

            // Show full error response for debugging
            $body = $response->body();
            $status = $response->status();

            // Try to extract JSON error message
            $json = $response->json();
            if ($json && isset($json['message'])) {
                $this->error("API Error [{$status}]: {$json['message']}");
            } else {
                // Truncate HTML responses to first meaningful line
                $cleanBody = strip_tags($body);
                $cleanBody = trim(preg_replace('/\s+/', ' ', $cleanBody));
                $this->error("API Error [{$status}]: " . substr($cleanBody, 0, 200));
            }

            Log::error("VPS Sync API Error [{$status}] for {$url}: {$body}");

            return null;
        } catch (\Exception $e) {
            $this->error("Connection Error: {$e->getMessage()}");
            Log::error("VPS Sync Connection Error for {$url}: {$e->getMessage()}");

            return null;
        }
    }
}
