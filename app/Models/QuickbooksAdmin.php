<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class QuickbooksAdmin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'quickbooks_admin';

    // Mass assignable attributes
    protected $guarded = [];

    // Hidden attributes for arrays
    protected $hidden = [
        'password', 'remember_token',
    ];

    // Casts for attributes
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Define the many-to-many relationship between Admin and Role
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'admin_role', 'admin_id', 'role_id');
    }

    // Check if the admin has a specific role
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    // Check if the admin has a specific permission
    public function hasPermission($permission)
    {
        // If the user has the Super Admin role, they have all permissions
        if ($this->roles()->where('name', 'Super Admin')->exists()) {
            return true;
        }

        // Otherwise, check if the permission exists in their roles
        return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('name', $permission);
        })->exists();
    }

    // Assign roles to the admin
    public function assignRole($role)
    {
        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    // Reset two-factor authentication code
    public function resetTwoFactorCode()
    {
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

    // Insert or update admins in the database
    public static function adminInsertUpdate($insert_admin_data)
    {
        $data = self::get();
        if (count($data) > 0) {
            foreach ($insert_admin_data as $key => $admin_details) {
                $result = self::updateOrCreate(
                    [
                        'admin_id' => $admin_details['admin_id'],
                    ],
                    $admin_details
                );
            }
        } else {
            $result = self::insert($insert_admin_data);
        }

        return $result;
    }
}


