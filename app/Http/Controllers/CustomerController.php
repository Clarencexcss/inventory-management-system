<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;

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
        $customer = Customer::create($request->all());

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

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer = auth()->user(); 
    
        // Gather data except photo and password
        $data = $request->except(['photo', 'password', 'password_confirmation']);
    
        // Only update password if the user entered one
        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }
    
        // Only update email if provided
        if (!$request->filled('email')) {
            unset($data['email']);
        }
    
        // Update customer with data
        $customer->update($data);
    
        // Handle photo upload
        if ($request->hasFile('photo')) {
            if ($customer->photo) {
                $oldPhotoPath = public_path('storage/customers/') . $customer->photo;
                if (file_exists($oldPhotoPath)) unlink($oldPhotoPath);
            }
    
            $file = $request->file('photo');
            $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('customers', $fileName, 'public');
    
            $customer->update(['photo' => $fileName]);
        }
    
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
}
