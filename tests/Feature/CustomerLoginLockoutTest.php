<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\LoginAttempt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerLoginLockoutTest extends TestCase
{
    use RefreshDatabase;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a customer for testing
        $this->customer = Customer::factory()->create([
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);
    }

    public function test_customer_account_gets_locked_after_three_failed_attempts()
    {
        // First failed attempt
        $response = $this->post('/customer/login', [
            'login' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['login' => 'Invalid credentials']);

        // Second failed attempt
        $response = $this->post('/customer/login', [
            'login' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['login' => 'Invalid credentials']);

        // Third failed attempt
        $response = $this->post('/customer/login', [
            'login' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['login' => 'Invalid credentials']);

        // Fourth attempt should be locked out
        $response = $this->post('/customer/login', [
            'login' => 'test@example.com',
            'password' => 'password123', // correct password
        ]);

        $response->assertSessionHasErrors(['login' => 'Account temporarily locked due to multiple failed login attempts. Please try again in 5 minutes.']);
    }

    public function test_customer_account_unlocks_after_5_minutes()
    {
        // Create 3 failed login attempts
        for ($i = 0; $i < 3; $i++) {
            LoginAttempt::create([
                'email' => 'test@example.com',
                'ip_address' => '127.0.0.1',
                'user_type' => 'customer',
                'attempted_at' => now()->subMinutes(6), // 6 minutes ago
                'successful' => false,
            ]);
        }

        // Login should work since the attempts are older than 5 minutes
        $response = $this->post('/customer/login', [
            'login' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Should redirect to dashboard
        $response->assertRedirect('/customer/dashboard');
    }

    public function test_successful_login_resets_failed_attempts_counter()
    {
        // Create 2 failed login attempts
        for ($i = 0; $i < 2; $i++) {
            LoginAttempt::create([
                'email' => 'test@example.com',
                'ip_address' => '127.0.0.1',
                'user_type' => 'customer',
                'attempted_at' => now(),
                'successful' => false,
            ]);
        }

        // Successful login
        $response = $this->post('/customer/login', [
            'login' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Should redirect to dashboard
        $response->assertRedirect('/customer/dashboard');

        // Check that we now have a successful login attempt recorded
        $this->assertDatabaseHas('login_attempts', [
            'email' => 'test@example.com',
            'user_type' => 'customer',
            'successful' => true,
        ]);

        // Another login attempt should work (counter reset)
        auth()->logout();
        $response = $this->post('/customer/login', [
            'login' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['login' => 'Invalid credentials']);
    }
}