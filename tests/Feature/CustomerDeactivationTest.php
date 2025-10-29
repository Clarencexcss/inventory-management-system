<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class CustomerDeactivationTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test that a customer can deactivate their account
     */
    public function test_customer_can_deactivate_account()
    {
        // Create a customer
        $customer = Customer::factory()->create([
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);
        
        // Login as the customer
        $this->actingAs($customer, 'web_customer');
        
        // Send deactivation request with correct password
        $response = $this->delete(route('customer.profile.deactivate'), [
            'password' => 'password123'
        ]);
        
        // Assert redirected to login page
        $response->assertRedirect(route('customer.login'));
        
        // Assert success message
        $response->assertSessionHas('success', 'Your account has been successfully deactivated. We\'re sorry to see you go.');
        
        // Refresh customer model
        $customer->refresh();
        
        // Assert customer is inactive
        $this->assertEquals('inactive', $customer->status);
        
        // Assert customer is soft deleted
        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }
    
    /**
     * Test that account deactivation fails with incorrect password
     */
    public function test_customer_cannot_deactivate_account_with_incorrect_password()
    {
        // Create a customer
        $customer = Customer::factory()->create([
            'password' => Hash::make('password123'),
            'status' => 'active'
        ]);
        
        // Login as the customer
        $this->actingAs($customer, 'web_customer');
        
        // Send deactivation request with incorrect password
        $response = $this->delete(route('customer.profile.deactivate'), [
            'password' => 'wrongpassword'
        ]);
        
        // Assert redirected back to profile page
        $response->assertRedirect(route('customer.profile'));
        
        // Assert error message
        $response->assertSessionHas('error', 'Incorrect password. Account was not deactivated.');
        
        // Refresh customer model
        $customer->refresh();
        
        // Assert customer is still active
        $this->assertEquals('active', $customer->status);
        
        // Assert customer is not soft deleted
        $this->assertNotSoftDeleted('customers', ['id' => $customer->id]);
    }
}