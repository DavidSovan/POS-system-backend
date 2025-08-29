<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];
    
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users() 
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Check if this is an admin role.
     */
    public function isAdmin(): bool
    {
        return $this->name === 'Admin';
    }

    /**
     * Check if this is a manager role.
     */
    public function isManager(): bool
    {
        return $this->name === 'Manager';
    }

    /**
     * Check if this is a cashier role.
     */
    public function isCashier(): bool
    {
        return $this->name === 'Cashier';
    }

    /**
     * Get role permissions based on role type.
     */

    // public function getPermissions(): array
    // {
    //     return match($this->name) {
    //         'Admin' => [
    //             'users.view', 'users.create', 'users.update', 'users.delete',
    //             'sales.view', 'sales.create', 'sales.update', 'sales.delete',
    //             'inventory.view', 'inventory.create', 'inventory.update', 'inventory.delete',
    //             'reports.view', 'system.config'
    //         ],
    //         'Manager' => [
    //             'sales.view', 'sales.create', 'sales.update', 'sales.delete',
    //             'inventory.view', 'inventory.create', 'inventory.update', 'inventory.delete',
    //             'reports.view'
    //         ],
    //         'Cashier' => [
    //             'sales.view', 'sales.create'
    //         ],
    //         default => []
    //     };
    // }
}