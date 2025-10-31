<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;

class EmailUniquenessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that customer cannot register with an email that already exists in users table
     *
     * @return void
     */
    public function test_customer_cannot_register_with_existing_user_email()
    {
        // Create a user (admin/staff) with a specific email
        $user = User::factory()->create([
            'email' => 'existing@example.com'
        ]);

        // Try to register a customer with the same email
        $response = $this->post('/customer/register', [
            'name' => 'Test Customer',
            'email' => 'existing@example.com',
            'username' => 'testcustomer',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '+639123456789',
            'address' => 'Test Address'
        ]);

        // Assert that the registration fails with the correct error message
        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHasErrors([
            'email' => '❌ The email address is already taken. Please use a different email.'
        ]);
        
        // Assert that no new customer was created
        $this->assertDatabaseMissing('customers', [
            'email' => 'existing@example.com'
        ]);
    }

    /**
     * Test that user (admin/staff) cannot register with an email that already exists in customers table
     *
     * @return void
     */
    public function test_user_cannot_register_with_existing_customer_email()
    {
        // Create a customer with a specific email
        $customer = Customer::factory()->create([
            'email' => 'existing@example.com'
        ]);

        // Try to register a user with the same email
        $response = $this->post('/users', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'username' => 'testuser',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert that the registration fails with the correct error message
        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHasErrors([
            'email' => '❌ The email address is already taken. Please use a different email.'
        ]);
        
        // Assert that no new user was created
        $this->assertDatabaseMissing('users', [
            'email' => 'existing@example.com'
        ]);
    }

    /**
     * Test that customer can register with a unique email
     *
     * @return void
     */
    public function test_customer_can_register_with_unique_email()
    {
        // Try to register a customer with a unique email
        $response = $this->post('/customer/register', [
            'name' => 'Test Customer',
            'email' => 'unique@example.com',
            'username' => 'testcustomer',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '+639123456789',
            'address' => 'Test Address'
        ]);

        // Assert that the registration is successful
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(); // Should redirect to dashboard
        
        // Assert that the customer was created
        $this->assertDatabaseHas('customers', [
            'email' => 'unique@example.com'
        ]);
    }

    /**
     * Test that user can register with a unique email
     *
     * @return void
     */
    public function test_user_can_register_with_unique_email()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin'
        ]);
        
        // Try to register a user with a unique email
        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'Test User',
            'email' => 'unique@example.com',
            'username' => 'testuser',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert that the registration is successful
        $response->assertSessionHasNoErrors();
        
        // Assert that the user was created
        $this->assertDatabaseHas('users', [
            'email' => 'unique@example.com'
        ]);
    }

    /**
     * Test that customer can update their email to a unique one
     *
     * @return void
     */
    public function test_customer_can_update_email_to_unique_value()
    {
        // Create a customer
        $customer = Customer::factory()->create([
            'email' => 'original@example.com'
        ]);

        // Try to update customer email to a unique value
        $response = $this->actingAs($customer, 'web_customer')->put("/customers/{$customer->id}", [
            'name' => $customer->name,
            'email' => 'newunique@example.com',
            'phone' => $customer->phone,
            'address' => $customer->address,
        ]);

        // Assert that the update is successful
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        // Assert that the customer email was updated
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'email' => 'newunique@example.com'
        ]);
    }

    /**
     * Test that customer cannot update their email to one that exists in users table
     *
     * @return void
     */
    public function test_customer_cannot_update_email_to_existing_user_email()
    {
        // Create a user with a specific email
        $user = User::factory()->create([
            'email' => 'existinguser@example.com'
        ]);

        // Create a customer
        $customer = Customer::factory()->create([
            'email' => 'originalcustomer@example.com'
        ]);

        // Try to update customer email to the existing user email
        $response = $this->actingAs($customer, 'web_customer')->put("/customers/{$customer->id}", [
            'name' => $customer->name,
            'email' => 'existinguser@example.com',
            'phone' => $customer->phone,
            'address' => $customer->address,
        ]);

        // Assert that the update fails with the correct error message
        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHasErrors([
            'email' => '❌ The email address is already taken. Please use a different email.'
        ]);
        
        // Assert that the customer email was not updated
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'email' => 'originalcustomer@example.com'
        ]);
    }

    /**
     * Test that user can update their email to a unique one
     *
     * @return void
     */
    public function test_user_can_update_email_to_unique_value()
    {
        // Create admin user
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin'
        ]);

        // Create a user
        $user = User::factory()->create([
            'email' => 'original@example.com'
        ]);

        // Try to update user email to a unique value
        $response = $this->actingAs($admin)->put("/users/{$user->id}", [
            'name' => $user->name,
            'email' => 'newunique@example.com',
            'username' => $user->username,
        ]);

        // Assert that the update is successful
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        
        // Assert that the user email was updated
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'newunique@example.com'
        ]);
    }

    /**
     * Test that user cannot update their email to one that exists in customers table
     *
     * @return void
     */
    public function test_user_cannot_update_email_to_existing_customer_email()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin'
        ]);

        // Create a customer with a specific email
        $customer = Customer::factory()->create([
            'email' => 'existingcustomer@example.com'
        ]);

        // Create a user
        $user = User::factory()->create([
            'email' => 'originaluser@example.com'
        ]);

        // Try to update user email to the existing customer email
        $response = $this->actingAs($admin)->put("/users/{$user->id}", [
            'name' => $user->name,
            'email' => 'existingcustomer@example.com',
            'username' => $user->username,
        ]);

        // Assert that the update fails with the correct error message
        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHasErrors([
            'email' => '❌ The email address is already taken. Please use a different email.'
        ]);
        
        // Assert that the user email was not updated
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'originaluser@example.com'
        ]);
    }
}