<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse; // Add this import statement

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Warehouse::insert([
            [
                'name' => 'Main Warehouse',
                'location' => 'Downtown',
                'capacity' => 10000,
                'lots' => 'A1,A2,A3,A4,A5',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'East Storage',
                'location' => 'East Side',
                'capacity' => 5000,
                'lots' => 'B1,B2,B3,B4,B5',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'West Depot',
                'location' => 'West City',
                'capacity' => 3000,
                'lots' => 'C1,C2,C3,C4,C5',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}