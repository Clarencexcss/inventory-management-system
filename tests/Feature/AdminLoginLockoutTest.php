<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LoginAttempt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginLockoutTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin for testing
        $this->admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    public function test_admin_account_gets_locked_after_three_failed_attempts()
    {
        // First failed attempt
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email' => 'These credentials do not match our records.']);

        // Second failed attempt
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email' => 'These credentials do not match our records.']);

        // Third failed attempt
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email' => 'These credentials do not match our records.']);

        // Fourth attempt should be locked out
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123', // correct password
        ]);

        $response->assertSessionHasErrors(['email' => 'Account temporarily locked due to multiple failed login attempts. Please try again in 5 minutes.']);
    }

    public function test_admin_account_unlocks_after_5_minutes()
    {
        // Create 3 failed login attempts
        for ($i = 0; $i < 3; $i++) {
            LoginAttempt::create([
                'email' => 'admin@example.com',
                'ip_address' => '127.0.0.1',
                'user_type' => 'admin',
                'attempted_at' => now()->subMinutes(6), // 6 minutes ago
                'successful' => false,
            ]);
        }

        // Login should work since the attempts are older than 5 minutes
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        // Should redirect to dashboard
        $response->assertRedirect('/dashboard');
    }

    public function test_successful_login_resets_failed_attempts_counter()
    {
        // Create 2 failed login attempts
        for ($i = 0; $i < 2; $i++) {
            LoginAttempt::create([
                'email' => 'admin@example.com',
                'ip_address' => '127.0.0.1',
                'user_type' => 'admin',
                'attempted_at' => now(),
                'successful' => false,
            ]);
        }

        // Successful login
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        // Should redirect to dashboard
        $response->assertRedirect('/dashboard');

        // Check that we now have a successful login attempt recorded
        $this->assertDatabaseHas('login_attempts', [
            'email' => 'admin@example.com',
            'user_type' => 'admin',
            'successful' => true,
        ]);

        // Another login attempt should work (counter reset)
        auth()->logout();
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email' => 'These credentials do not match our records.']);
    }
}