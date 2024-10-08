<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Faker\Factory as Faker;

class UpdateUserDetails extends Command
{
    protected $signature = 'update:user-details';
    protected $description = 'Update users\' details';

    public function handle()
    {
        $faker = Faker::create();
        $timezones = ['CET', 'CST', 'GMT+1'];

        $users = User::all();

        foreach ($users as $user) {
            $user->update([
                'name' => $faker->name,
                'timezone' => $timezones[array_rand($timezones)]
            ]);
        }

        $this->info('User details updated successfully.');
    }
}
