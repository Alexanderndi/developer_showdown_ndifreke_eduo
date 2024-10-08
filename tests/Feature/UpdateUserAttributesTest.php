<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Jobs\UpdateUserAttributes;
use Illuminate\Support\Facades\Http;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateUserAttributesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_user_attributes_update_success()
    {
        // Arrange: Create a user and mock the HTTP response
        $user = User::factory()->create();
        Http::fake([
            'https://api.example.com/batches' => Http::sequence()
                ->push(['success' => true], 200),
        ]);

        // Act: Dispatch the job
        UpdateUserAttributes::dispatch($user);

        // Assert: Check if the HTTP request was made
        Http::assertSent(function ($request) use ($user) {
            return $request->url() === 'https://api.example.com/batches' &&
                   $request->data['batches'][0]['subscribers'][0]['email'] === $user->email;
        });
    }

    public function test_user_attributes_update_failure()
    {
        // Arrange: Create a user and mock the HTTP failure response
        $user = User::factory()->create();
        Http::fake([
            'https://api.example.com/batches' => Http::sequence()
                ->push(['success' => false], 500),
        ]);

        // Act: Dispatch the job
        UpdateUserAttributes::dispatch($user);

        // Assert: Check if the HTTP request was made
        Http::assertSent(function ($request) use ($user) {
            return $request->url() === 'https://api.example.com/batches' &&
                   $request->data['batches'][0]['subscribers'][0]['email'] === $user->email;
        });
    }

    public function test_user_update_rate_limiting() {
        // Arrange: Create a user
        $user = User::factory()->create();

        // Set the rate limit
        RateLimiter::for('api-updates', function ($job) use ($user) {
            return Limit::perMinute(1)->by($user->id);
        });

        // Act: Dispatch the job twice quickly
        UpdateUserAttributes::dispatch($user);
        UpdateUserAttributes::dispatch($user);

        Http::fake([
            'https://api.example.com/batches' => Http::sequence()
                ->push(['success' => true], 200),
        ]);

        UpdateUserAttributes::dispatch($user);

        // Assert that the job was sent and handled correctly
        Http::assertSent(function ($request) use ($user) {
            return $request->url() === 'https://api.example.com/batches' &&
                   $request->data['batches'][0]['subscribers'][0]['email'] === $user->email;
        });
    }
}
