<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\RateLimiter;

class UpdateUserAttributes implements ShouldQueue
{
    use Queueable;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function handle(): void
    {
        // Define the rate limiter key using user's ID
        $key = 'api-updates:' . $this->user->id;

        // Check if too many attempts have been made
        if (RateLimiter::tooManyAttempts($key, 60)) {
            // If the limit is exceeded, delay the job or handle as needed
            return $this->release(60);  // Release the job and retry after 60 seconds
        }

        // Perform the API request if the limit has not been exceeded
        $payload = [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'timezone' => $this->user->timezone,
        ];

        $response = Http::post('https://api.example.com/batches', [
            'batches' => [
                'subscribers' => [$payload]
            ]
        ]);

        if ($response->successful()) {
            // If the request is successful, hit the rate limiter to track this request
            RateLimiter::hit($key);
        } else {
            Log::error('Failed to update user attributes', [
                'user_id' => $this->user->id,
                'response_code' => $response->status(),
                'response_body' => $response->body(),
            ]);
        }
    }
}
