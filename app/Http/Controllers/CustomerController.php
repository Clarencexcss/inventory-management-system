<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();

        return view('customers.index', [
            'customers' => $customers
        ]);
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        // Process phone number - if it starts with 09, convert to +63
        $requestData = $request->all();
        if (isset($requestData['phone']) && preg_match('/^09\d{9}$/', $requestData['phone'])) {
            $requestData['phone'] = '+63' . substr($requestData['phone'], 1);
        }

        $customer = Customer::create($requestData);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();

            $file->storeAs('customers/', $filename, 'public');
            $customer->update([
                'photo' => $filename
            ]);
        }

        return redirect()
            ->route('customers.index')
            ->with('success', 'New customer has been created!');
    }

    public function show(Customer $customer)
    {
        $customer->loadMissing(['quotations', 'orders'])->get();

        return view('customers.show', [
            'customer' => $customer
        ]);
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', [
            'customer' => $customer
        ]);
    }

    public function update(UpdateCustomerRequest $request)
    {
        $customer = auth()->user(); 
    
        // Prepare data for update
        $data = $request->only(['name', 'username', 'email', 'phone', 'address']);
        
        // Process phone number - if it starts with 09, convert to +63
        if (isset($data['phone']) && preg_match('/^09\d{9}$/', $data['phone'])) {
            $data['phone'] = '+63' . substr($data['phone'], 1);
        }
    
        // Handle password update - only if provided
        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($customer->photo) {
                Storage::disk('public')->delete('customers/' . $customer->photo);
            }
            
            // Store new photo
            $file = $request->file('photo');
            $filename = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('customers', $filename, 'public');
            $data['photo'] = $filename;
        }
        
        // Update customer with all provided data
        $customer->update($data);
    
        return redirect()
            ->route('customer.profile')
            ->with('success', 'Profile has been updated successfully!');
    }
    
    /**
     * Show customer profile (for authenticated customers)
     */
    public function profile()
    {
        $customer = auth()->user();

        return view('customer.profile', [
            'customer' => $customer
        ]);
    }
    
    /**
     * Deactivate customer account
     */
    public function deactivate(Request $request)
    {
        $customer = auth()->user();
        
        // Validate password
        $request->validate([
            'password' => 'required|string'
        ]);
        
        // Check if password is correct
        if (!Hash::check($request->password, $customer->password)) {
            return redirect()
                ->route('customer.profile')
                ->with('error', 'Incorrect password. Account was not deactivated.');
        }
        
        // Check if customer has pending orders
        $pendingOrders = $customer->pendingOrders()->count();
        if ($pendingOrders > 0) {
            return redirect()
                ->route('customer.profile')
                ->with('error', 'You have pending orders. Please complete or cancel them before deactivating your account.');
        }
        
        // Deactivate account by setting status to inactive and soft deleting
        $customer->update([
            'status' => 'inactive'
        ]);
        
        $customer->delete(); // This will soft delete the account
        
        // Logout the customer
        auth()->logout();
        
        return redirect()
            ->route('customer.login')
            ->with('success', 'Your account has been successfully deactivated. We\'re sorry to see you go.');
    }
}