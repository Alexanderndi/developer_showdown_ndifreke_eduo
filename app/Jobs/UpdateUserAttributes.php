<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class UpdateUserAttributes implements ShouldQueue
{
    use Queueable;

    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Rate limit check
        if (RateLimiter::tooManyAttempts('api-updates:' . $this->user->id, 60)) {
            Log::warning('Rate limit exceeded for user', ['user_id' => $this->user->id]);
            return; // Exit the job if the limit is exceeded
        }

        // Make the API request
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

        if ($response->failed()) {
            // Handle failure - log the error or retry later
            Log::error('Failed to update user attributes', [
                'user_id' => $this->user->id,
                'response' => $response->body(),
            ]);
        } else {
            // Increment the rate limit on a successful response
            RateLimiter::hit('api-updates:' . $this->user->id);
        }
    }
}
