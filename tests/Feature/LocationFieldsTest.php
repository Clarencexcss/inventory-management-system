    /** @test */
    public function location_fields_are_saved_correctly()
    {
        // Create a customer
        $customer = Customer::factory()->create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '09123456789'
        ]);

        // Create a product
        $product = Product::factory()->create([
            'quantity' => 10,
            'selling_price' => 100
        ]);

        // Add product to cart
        Cart::instance('customer')->add($product->id, $product->name, 1, $product->selling_price);

        // Login as customer
        $this->actingAs($customer, 'web_customer');

        // Submit checkout form with location data
        $response = $this->post(route('customer.checkout.place-order'), [
            'receiver_name' => 'John Doe',
            'contact_phone' => '09123456789',
            'city' => 'Cabuyao',
            'postal_code' => '4025',
            'barangay' => 'Barangay Uno (Poblacion)',
            'street_name' => 'Main Street',
            'building' => 'Building A',
            'house_no' => '123',
            'payment_type' => 'cash'
        ]);

        // Assert redirect to orders page
        $response->assertRedirect(route('customer.orders'));

        // Assert order was created with location data
        $order = $customer->orders()->first();
        $this->assertNotNull($order);
        
        $this->assertEquals('Test Customer', $order->customer_name);
        $this->assertEquals('John Doe', $order->receiver_name);
        $this->assertEquals('test@example.com', $order->customer_email);
        $this->assertEquals('Cabuyao', $order->city);
        $this->assertEquals('4025', $order->postal_code);
        $this->assertEquals('Barangay Uno (Poblacion)', $order->barangay);
        $this->assertEquals('Main Street', $order->street_name);
        $this->assertEquals('Building A', $order->building);
        $this->assertEquals('123', $order->house_no);
        
        // Assert delivery address was constructed correctly
        $this->assertEquals('123, Building A, Main Street, Barangay Uno (Poblacion), Cabuyao, Laguna, 4025', $order->delivery_address);

        // Clear cart
        Cart::instance('customer')->destroy();
    }
    
    /** @test */
    public function location_fields_display_correctly_in_admin_view()
    {
        // Create a customer
        $customer = Customer::factory()->create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '09123456789'
        ]);

        // Create a product
        $product = Product::factory()->create([
            'quantity' => 10,
            'selling_price' => 100
        ]);

        // Add product to cart
        Cart::instance('customer')->add($product->id, $product->name, 1, $product->selling_price);

        // Login as customer
        $this->actingAs($customer, 'web_customer');

        // Submit checkout form with location data
        $this->post(route('customer.checkout.place-order'), [
            'full_name' => 'Test Customer',
            'email' => 'test@example.com',
            'contact_phone' => '09123456789',
            'city' => 'Cabuyao',
            'postal_code' => '4025',
            'barangay' => 'Barangay Uno (Poblacion)',
            'street_name' => 'Main Street',
            'building' => 'Building A',
            'house_no' => '123',
            'payment_type' => 'cash'
        ]);

        // Get the created order
        $order = $customer->orders()->first();
        
        // Login as admin
        $admin = \App\Models\User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin, 'web');
        
        // Visit the order details page
        $response = $this->get(route('orders.show', $order));
        
        // Assert that all location fields are displayed
        $response->assertSee('Cabuyao');
        $response->assertSee('4025');
        $response->assertSee('Barangay Uno (Poblacion)');
        $response->assertSee('Main Street');
        $response->assertSee('Building A');
        $response->assertSee('123');
        $response->assertSee('123, Building A, Main Street, Barangay Uno (Poblacion), Cabuyao, Laguna, 4025');
    }
    
    /** @test */
    public function receiver_name_functionality_works_correctly()
    {
        // Create a customer
        $customer = Customer::factory()->create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '09123456789'
        ]);

        // Create a product
        $product = Product::factory()->create([
            'quantity' => 10,
            'selling_price' => 100
        ]);

        // Add product to cart
        Cart::instance('customer')->add($product->id, $product->name, 1, $product->selling_price);

        // Login as customer
        $this->actingAs($customer, 'web_customer');

        // Submit checkout form with receiver name
        $response = $this->post(route('customer.checkout.place-order'), [
            'receiver_name' => 'John Doe',
            'contact_phone' => '09123456789',
            'city' => 'Cabuyao',
            'postal_code' => '4025',
            'barangay' => 'Barangay Uno (Poblacion)',
            'street_name' => 'Main Street',
            'payment_type' => 'cash'
        ]);

        // Assert redirect to orders page
        $response->assertRedirect(route('customer.orders'));

        // Get the created order
        $order = $customer->orders()->first();
        
        // Assert receiver name was saved
        $this->assertEquals('John Doe', $order->receiver_name);
        
        // Login as admin
        $admin = \App\Models\User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin, 'web');
        
        // Visit the order details page
        $response = $this->get(route('orders.show', $order));
        
        // Assert that receiver name is displayed
        $response->assertSee('Receiver Name');
        $response->assertSee('John Doe');
        
        // Clear cart
        Cart::instance('customer')->destroy();
    }
    
    /** @test */
    public function contact_phone_validation_works_correctly()
    {
        // Create a customer
        $customer = Customer::factory()->create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '09123456789'
        ]);

        // Create a product
        $product = Product::factory()->create([
            'quantity' => 10,
            'selling_price' => 100
        ]);

        // Add product to cart
        Cart::instance('customer')->add($product->id, $product->name, 1, $product->selling_price);

        // Login as customer
        $this->actingAs($customer, 'web_customer');

        // Try with invalid phone number (too short)
        $response = $this->post(route('customer.checkout.place-order'), [
            'full_name' => 'Test Customer',
            'email' => 'test@example.com',
            'contact_phone' => '0912345678', // 10 digits - invalid
            'city' => 'Cabuyao',
            'postal_code' => '4025',
            'barangay' => 'Barangay Uno (Poblacion)',
            'street_name' => 'Main Street',
            'payment_type' => 'cash'
        ]);

        // Should get validation error
        $response->assertSessionHasErrors('contact_phone');

        // Try with invalid phone number (too long for 09 format)
        $response = $this->post(route('customer.checkout.place-order'), [
            'full_name' => 'Test Customer',
            'email' => 'test@example.com',
            'contact_phone' => '091234567890', // 12 digits - invalid
            'city' => 'Cabuyao',
            'postal_code' => '4025',
            'barangay' => 'Barangay Uno (Poblacion)',
            'street_name' => 'Main Street',
            'payment_type' => 'cash'
        ]);

        // Should get validation error
        $response->assertSessionHasErrors('contact_phone');

        // Try with invalid phone number (too long for +63 format)
        $response = $this->post(route('customer.checkout.place-order'), [
            'full_name' => 'Test Customer',
            'email' => 'test@example.com',
            'contact_phone' => '+63912345678901', // 14 digits - invalid
            'city' => 'Cabuyao',
            'postal_code' => '4025',
            'barangay' => 'Barangay Uno (Poblacion)',
            'street_name' => 'Main Street',
            'payment_type' => 'cash'
        ]);

        // Should get validation error
        $response->assertSessionHasErrors('contact_phone');

        // Try with valid 09 format
        $response = $this->post(route('customer.checkout.place-order'), [
            'full_name' => 'Test Customer',
            'email' => 'test@example.com',
            'contact_phone' => '09123456789', // 11 digits - valid
            'city' => 'Cabuyao',
            'postal_code' => '4025',
            'barangay' => 'Barangay Uno (Poblacion)',
            'street_name' => 'Main Street',
            'payment_type' => 'cash'
        ]);

        // Should be successful
        $response->assertRedirect(route('customer.orders'));
        
        // Try with valid +63 format
        $response = $this->post(route('customer.checkout.place-order'), [
            'full_name' => 'Test Customer',
            'email' => 'test@example.com',
            'contact_phone' => '+639123456789', // 12 digits - valid
            'city' => 'Cabuyao',
            'postal_code' => '4025',
            'barangay' => 'Barangay Uno (Poblacion)',
            'street_name' => 'Main Street',
            'payment_type' => 'cash'
        ]);

        // Should be successful
        $response->assertRedirect(route('customer.orders'));
    }
</```