<?php

namespace Database\Seeders;

use App\Models\User;

use App\Models\Town;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Town::create(['name' => 'Example1', 'description' => 'This is an example town!']);
        Town::create(['name' => 'Example2', 'description' => 'This is another example town.']);
    }
}
