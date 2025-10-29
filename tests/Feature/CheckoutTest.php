    /** @test */
    public function contact_phone_must_be_valid_format()
    {
        // Create a customer
        $customer = Customer::factory()->create();

        // Create a product
        $product = Product::factory()->create([
            'quantity' => 10,
            'selling_price' => 100
        ]);

        // Add product to cart
        Cart::instance('customer')->add($product->id, $product->name, 1, $product->selling_price);

        // Login as customer
        $this->actingAs($customer, 'web_customer');

        // Try with less than 11 digits
        $response = $this->post(route('customer.checkout.place-order'), [
            'receiver_name' => 'John Doe',
            'contact_phone' => '1234567890', // 10 digits
            'city' => 'Cabuyao',
            'postal_code' => '4025',
            'barangay' => 'Barangay Uno (Poblacion)',
            'street_name' => 'Main Street',
            'payment_type' => 'cash'
        ]);

        // Should get validation error
        $response->assertSessionHasErrors('contact_phone');

        // Try with more than 11 digits (09 format)
        $response = $this->post(route('customer.checkout.place-order'), [
            'receiver_name' => 'John Doe',
            'contact_phone' => '091234567890', // 12 digits
            'city' => 'Cabuyao',
            'postal_code' => '4025',
            'barangay' => 'Barangay Uno (Poblacion)',
            'street_name' => 'Main Street',
            'payment_type' => 'cash'
        ]);

        // Should get validation error
        $response->assertSessionHasErrors('contact_phone');

        // Try with more than 12 digits (+63 format)
        $response = $this->post(route('customer.checkout.place-order'), [
            'receiver_name' => 'John Doe',
            'contact_phone' => '+6391234567890', // 13 digits
            'city' => 'Cabuyao',
            'postal_code' => '4025',
            'barangay' => 'Barangay Uno (Poblacion)',
            'street_name' => 'Main Street',
            'payment_type' => 'cash'
        ]);

        // Should get validation error
        $response->assertSessionHasErrors('contact_phone');

        // Try with exactly 11 digits (09 format)
        $response = $this->post(route('customer.checkout.place-order'), [
            'receiver_name' => 'John Doe',
            'contact_phone' => '09123456789', // 11 digits
            'city' => 'Cabuyao',
            'postal_code' => '4025',
            'barangay' => 'Barangay Uno (Poblacion)',
            'street_name' => 'Main Street',
            'payment_type' => 'cash'
        ]);

        // Should be successful
        $response->assertRedirect(route('customer.orders'));
        
        // Try with +63 format (12 characters)
        $response = $this->post(route('customer.checkout.place-order'), [
            'receiver_name' => 'John Doe',
            'contact_phone' => '+639123456789', // +63 format
            'city' => 'Cabuyao',
            'postal_code' => '4025',
            'barangay' => 'Barangay Uno (Poblacion)',
            'street_name' => 'Main Street',
            'payment_type' => 'cash'
        ]);

        // Should be successful
        $response->assertRedirect(route('customer.orders'));

        // Clear cart
        Cart::instance('customer')->destroy();
    }
    
    /** @test */
    public function location_fields_are_required()
    {
        // Create a customer
        $customer = Customer::factory()->create();

        // Create a product
        $product = Product::factory()->create([
            'quantity' => 10,
            'selling_price' => 100
        ]);

        // Add product to cart
        Cart::instance('customer')->add($product->id, $product->name, 1, $product->selling_price);

        // Login as customer
        $this->actingAs($customer, 'web_customer');

        // Try without required location fields
        $response = $this->post(route('customer.checkout.place-order'), [
            'receiver_name' => 'John Doe',
            'contact_phone' => '09123456789',
            'payment_type' => 'cash'
        ]);

        // Should get validation errors for location fields
        $response->assertSessionHasErrors(['city', 'postal_code', 'barangay', 'street_name']);

        // Clear cart
        Cart::instance('customer')->destroy();
    }