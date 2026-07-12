<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * API Controller for receiving sync data from local Checkpoint GIIC.
 *
 * Accepts HTTPS POST requests with checkpoint, user, and employee data
 * from the local server and upserts them into the VPS database.
 *
 * Endpoints:
 *   POST /api/sync/checkpoints  — Sync checkpoint records
 *   POST /api/sync/users        — Sync user records
 *   POST /api/sync/employees    — Sync employee records
 */
class SyncApiController extends Controller
{
    /**
     * POST /api/sync/checkpoints
     *
     * Receives an array of checkpoint records and upserts them
     * into the local checkpoints table (checkpoints on VPS).
     */
    public function syncCheckpoints(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'checkpoints' => 'required|array|min:1',
            'checkpoints.*.id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $checkpoints = $request->input('checkpoints');
        $syncedIds = [];
        $failedIds = [];

        foreach ($checkpoints as $data) {
            try {
                $id = $data['id'];

                // Remove fields that should not be synced to VPS
                $syncData = collect($data)->except(['sync'])->toArray();

                DB::table('checkpoints_giic')->updateOrInsert(
                    ['id' => $id],
                    $syncData
                );

                $syncedIds[] = $id;
            } catch (\Exception $e) {
                Log::error("Sync checkpoint failed for ID {$data['id']}: " . $e->getMessage());
                $failedIds[] = [
                    'id' => $data['id'],
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'synced_count' => count($syncedIds),
            'synced_ids' => $syncedIds,
            'failed_count' => count($failedIds),
            'failed' => $failedIds,
        ]);
    }

    /**
     * POST /api/sync/users
     *
     * Receives an array of user records and upserts them
     * into the users table on VPS.
     */
    public function syncUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users' => 'required|array|min:1',
            'users.*.id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $users = $request->input('users');
        $syncedIds = [];
        $failedIds = [];

        foreach ($users as $data) {
            try {
                $id = $data['id'];

                // Remove sensitive/unnecessary fields
                $syncData = collect($data)->except(['remember_token'])->toArray();

                DB::table('users')->updateOrInsert(
                    ['id' => $id],
                    $syncData
                );

                $syncedIds[] = $id;
            } catch (\Exception $e) {
                Log::error("Sync user failed for ID {$data['id']}: " . $e->getMessage());
                $failedIds[] = [
                    'id' => $data['id'],
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'synced_count' => count($syncedIds),
            'synced_ids' => $syncedIds,
            'failed_count' => count($failedIds),
            'failed' => $failedIds,
        ]);
    }

    /**
     * POST /api/sync/employees
     *
     * Receives an array of employee records and upserts them
     * into the employees table on VPS.
     */
    public function syncEmployees(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employees' => 'required|array|min:1',
            'employees.*.id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $employees = $request->input('employees');
        $syncedIds = [];
        $failedIds = [];

        foreach ($employees as $data) {
            try {
                $id = $data['id'];
                $syncData = $data;

                DB::table('employees')->updateOrInsert(
                    ['id' => $id],
                    $syncData
                );

                $syncedIds[] = $id;
            } catch (\Exception $e) {
                Log::error("Sync employee failed for ID {$data['id']}: " . $e->getMessage());
                $failedIds[] = [
                    'id' => $data['id'],
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'synced_count' => count($syncedIds),
            'synced_ids' => $syncedIds,
            'failed_count' => count($failedIds),
            'failed' => $failedIds,
        ]);
    }
}
