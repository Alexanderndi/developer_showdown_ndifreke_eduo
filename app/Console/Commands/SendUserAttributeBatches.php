<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SendUserAttributeBatches extends Command
{
    protected $signature = 'send:user-batches';
    protected $description = 'Send user attribute updates in batches to third-party API';

    public function handle()
    {
        $users = User::where('updated_at', '>=', now()->subHour())  // Find users updated in the last hour
            ->take(1000)
            ->get();

            $payload = [
                'batches' => [
                    'subscribers' => $users->map(function ($user) {
                        return [
                            'email' => $user->email,
                            'name' => $user->name,
                            'time_zone' => $user->timezone,
                        ];
                    })->toArray()
                ]
            ];


            // Send the batch request using the example url
            $response = Http::post('https://api.example.com/batches', $payload);

            if ($response->successful()) {
                $this->info('Batch successfully sent.');
            } else {
                $this->error('Batch sending failed.');
            }
    }
}
