<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuickbooksAdmin;
use Illuminate\Support\Facades\Hash;

class QuickbooksAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        QuickbooksAdmin::create([
            'name' => 'CWI Admin',
            'email' => 'admin@colorwrap.inc',
            'password' => Hash::make('12345678!!..'), // Securely hash the password
            'two_factor_code' => null, // Assuming 2FA is optional or generated dynamically
            'two_factor_expires_at' => null, // Null until 2FA is generated
        ]);
    }
}
