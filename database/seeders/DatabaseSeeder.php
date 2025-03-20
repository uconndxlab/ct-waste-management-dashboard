<?php

namespace Database\Seeders;

use App\Models\User;

use App\Models\Municipalities;
use App\Models\Municipality;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run() {
        $municipalities = [
            [
                'name' => 'Municipality One',
                'year' => 2023,
                'bulky_waste' => 1200.50,
                'recycling' => 800.25,
                'tipping_fees' => 450.30,
                'admin_costs' => 350.00,
                'hazardous_waste' => 100.50,
                'contractual_services' => 200.00,
                'landfill_costs' => 500.00,
                'total_sanitation_refuse' => 2000.00,
                'only_public_works' => 1500.00,
                'transfer_station_wages' => 250.00,
                'hauling_fees' => 350.00,
                'curbside_pickup_fees' => 400.00,
                'waste_collection' => 1000.00,
                'notes' => 'This is a sample note for Municipality One.'
            ],
            [
                'name' => 'Municipality Two',
                'year' => 2023,
                'bulky_waste' => 1500.75,
                'recycling' => 900.50,
                'tipping_fees' => 600.00,
                'admin_costs' => 400.00,
                'hazardous_waste' => 120.00,
                'contractual_services' => 250.00,
                'landfill_costs' => 550.00,
                'total_sanitation_refuse' => 2200.00,
                'only_public_works' => 1600.00,
                'transfer_station_wages' => 300.00,
                'hauling_fees' => 400.00,
                'curbside_pickup_fees' => 450.00,
                'waste_collection' => 1100.00,
                'notes' => 'This is a sample note for Municipality Two.'
            ],
            // Add more municipalities as needed
        ];

        // Insert the data into the municipalities table
        foreach ($municipalities as $municipality) {
            Municipality::create($municipality);
        }
    }
}
