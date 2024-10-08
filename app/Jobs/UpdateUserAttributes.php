<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;

class UpdateUserAttributes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        // Make the API request here
        $payload = [
            'email' => $this->user->email,
            'name' => $this->user->name,
            'time_zone' => $this->user->timezone,
        ];

        // Call the API using HTTP Client
        $response = Http::post('https://api.example.com/batches', [
            'batches' => [
                'subscribers' => [$payload]
            ]
        ]);

        if ($response->failed()) {
            // failure handler here
        }
    }
}
