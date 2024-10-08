<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Example timezones
        $timezones = ['CET', 'CST', 'GMT+1'];

        // Seed 20 users
        for ($i = 0; $i < 20; $i++) {
            User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'timezone' => $timezones[array_rand($timezones)], // Randomly assign a timezone
                'password' => Hash::make('password'), // Provide a hashed default password
            ]);
        }
    }
}
