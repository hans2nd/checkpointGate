<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Checkpoint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncCheckpointsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:checkpoints';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync checkpoints data to VPS database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting checkpoint synchronization...');

        // Get all unsynced checkpoints
        $unsynced = Checkpoint::where('sync', 0)->get();

        if ($unsynced->isEmpty()) {
            $this->info('No data to sync.');
            return;
        }

        $this->info("Found {$unsynced->count()} records to sync.");

        foreach ($unsynced as $checkpoint) {
            try {
                $data = $checkpoint->getAttributes();

                // Assuming the remote table has the same structure and we mark it as synced there too
                $data['sync'] = 1;

                // Insert or update on remote VPS
                DB::connection('vps_mysql')->table('checkpoints_giic')->updateOrInsert(
                    ['id' => $checkpoint->id],
                    $data
                );

                // Update local sync status
                $checkpoint->update(['sync' => 1]);

                $this->info("Synced checkpoint ID: {$checkpoint->id}");
            } catch (\Exception $e) {
                $this->error("Failed to sync checkpoint ID: {$checkpoint->id}. Error: " . $e->getMessage());
                Log::error("Checkpoint Sync Error (ID {$checkpoint->id}): " . $e->getMessage());
            }
        }

        $this->info('Synchronization completed.');
    }
}
