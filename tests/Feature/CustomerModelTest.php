<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Customer;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerModelTest extends TestCase
{
    /**
     * Test that Customer model uses SoftDeletes trait
     */
    public function test_customer_model_uses_soft_deletes()
    {
        $uses = class_uses(Customer::class);
        $this->assertContains(SoftDeletes::class, $uses);
    }
    
    /**
     * Test that Customer model has status and role attributes
     */
    public function test_customer_model_has_status_and_role_attributes()
    {
        $customer = new Customer();
        
        // Check that fillable includes status and role
        $this->assertContains('status', $customer->getFillable());
        $this->assertContains('role', $customer->getFillable());
    }
}