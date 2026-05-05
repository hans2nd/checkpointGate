<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'employee_id',
        'name',
        'role_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Employees have no password — disable password hashing.
     */
    protected $hidden = [];

    /**
     * Get the role associated with this employee.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the linked user (if any).
     */
    public function user()
    {
        return $this->hasOne(User::class, 'employee_id');
    }

    /**
     * Check if employee has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Check if employee has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->role && in_array($this->role->name, $roles);
    }

    /**
     * Check if employee has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->role && $this->role->hasPermission($permission);
    }

    /**
     * Check if employee is admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('administrator') || $this->hasRole('admin');
    }
}
