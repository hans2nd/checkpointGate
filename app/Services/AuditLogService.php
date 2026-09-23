<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    /**
     * Record an audit log entry.
     *
     * @param string      $action      e.g. login, logout, create, update, delete, trigger_start, cancel, approve_cancel ...
     * @param string      $module      e.g. auth, checkpoint, vehicle, gate, user, employee
     * @param string      $description Human-readable description
     * @param Model|null  $auditable   The model being acted upon (Checkpoint, User, etc.)
     * @param array|null  $oldValues   Snapshot of data before change
     * @param array|null  $newValues   Snapshot of data after change
     * @param int|null    $userId      Override user ID (for login failures where Auth is not set)
     * @param string|null $userName    Override user name
     * @param string|null $userEmail   Override user email
     */
    public static function log(
        string $action,
        string $module,
        string $description,
        ?Model $auditable = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $userId = null,
        ?string $userName = null,
        ?string $userEmail = null,
    ): AuditLog {
        $request = request();
        $user = Auth::user();

        return AuditLog::create([
            'user_id'         => $userId ?? $user?->id,
            'user_name'       => $userName ?? $user?->name,
            'user_email'      => $userEmail ?? $user?->email,
            'action'          => $action,
            'module'          => $module,
            'description'     => $description,
            'auditable_id'    => $auditable?->getKey(),
            'auditable_type'  => $auditable ? get_class($auditable) : null,
            'old_values'      => $oldValues,
            'new_values'      => $newValues,
            'ip_address'      => $request?->ip(),
            'user_agent'      => $request?->userAgent(),
            'channel'         => self::detectChannel(),
        ]);
    }

    /**
     * Detect whether the request is from API or web.
     */
    protected static function detectChannel(): string
    {
        $request = request();

        if ($request && $request->is('api/*')) {
            return 'api';
        }

        return 'web';
    }
}
