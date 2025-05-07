<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Many-to-many relationship with permissions
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    // Many-to-many relationship with admins
    public function admins()
{
    return $this->belongsToMany(QuickbooksAdmin::class, 'admin_role', 'role_id', 'admin_id');
}


    public function isSuperAdmin()
    {
        return $this->name === 'Super Admin'; // Assuming the role's name is 'Super Admin'
    }
}
