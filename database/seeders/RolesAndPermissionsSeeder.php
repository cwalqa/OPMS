<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $roles = [
            'Super Admin',
            'Front Desk Personnel',
            'Line Scheduler',
            'Production Manager',
            'Delivery Agent'
        ];
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        // Create permissions
        $permissions = [
            'user_management',
            'order_management',
            'line_scheduling',
            'production_management',
            'packaging_delivery'
        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
