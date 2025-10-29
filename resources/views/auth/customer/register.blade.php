@extends('layouts.auth')

@section('content')
<div class="card card-md">
    <div class="card-body">
        <h2 class="h2 text-center mb-4">
            Create Customer Account
        </h2>
        {{-- Error and session message display --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <form action="{{ route('customer.register') }}" method="POST" autocomplete="off" id="registrationForm">
            @csrf
            <x-input name="name" :value="old('name')" placeholder="Full Name" required="true" id="nameInput"/>
            <small class="form-text text-muted" id="nameHelp">Name may only contain letters, spaces, periods, hyphens, and apostrophes.</small>
            <x-input name="email" :value="old('email')" placeholder="your@email.com" required="true"/>
            <x-input name="username" :value="old('username')" placeholder="Username" required="true"/>
            <x-input type="password" name="password" placeholder="Password" required="true"/>
            <x-input type="password" name="password_confirmation" placeholder="Confirm Password" required="true"/>
            <x-input name="phone" :value="old('phone')" placeholder="e.g., 09123456789 or +63123456789" required="true" id="phoneInput"/>
            <small class="form-text text-muted">Phone number must start with +63 and be exactly 11 digits. If you type "09", it will be automatically converted to "+63".</small>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3" placeholder="Your address" required>{{ old('address') }}</textarea>
            </div>
            <div class="form-footer">
                <x-button type="submit" class="w-100">
                    {{ __('Create Account') }}
                </x-button>
            </div>
        </form>
    </div>
</div>
<div class="text-center mt-3 text-gray-600">
    <p>Already have an account?
        <a href="{{ route('customer.login') }}" class="text-blue-500 hover:text-blue-700 focus:outline-none focus:underline" tabindex="-1">
            Sign in
        </a>
    </p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Name validation
    const nameInput = document.getElementById('nameInput');
    if (nameInput) {
        nameInput.addEventListener('input', function() {
            const nameValue = this.value;
            const nameErrorId = 'name-error';
            let errorElement = document.getElementById(nameErrorId);
            
            // Remove existing error message
            if (errorElement) {
                errorElement.remove();
            }
            
            // Check for invalid characters
            const validNamePattern = /^[a-zA-Z\s.\-']*$/;
            if (nameValue && !validNamePattern.test(nameValue)) {
                // Create error message element
                errorElement = document.createElement('div');
                errorElement.id = nameErrorId;
                errorElement.className = 'text-danger mt-1';
                errorElement.textContent = 'Name may only contain letters, spaces, periods, hyphens, and apostrophes.';
                
                // Insert after the name input
                this.parentNode.insertBefore(errorElement, this.nextSibling);
            }
        });
    }
    
    // Phone validation
    const phoneInput = document.getElementById('phoneInput');
    if (phoneInput) {
        phoneInput.addEventListener('blur', function() {
            let phoneNumber = this.value.trim();
            
            // If the phone number starts with "09" and is 11 digits, convert to +63 format
            if (phoneNumber.startsWith('09') && phoneNumber.length === 11) {
                this.value = '+63' + phoneNumber.substring(1);
            }
            
            // Check for invalid length and add visual feedback
            const phoneErrorId = 'phone-error';
            let errorElement = document.getElementById(phoneErrorId);
            
            // Remove existing error message
            if (errorElement) {
                errorElement.remove();
            }
            
            // Add error message if phone number is too long
            if ((phoneNumber.length > 11 && !phoneNumber.startsWith('+63')) || 
                (phoneNumber.startsWith('+63') && phoneNumber.length > 13)) {
                // Create error message element
                errorElement = document.createElement('div');
                errorElement.id = phoneErrorId;
                errorElement.className = 'text-danger mt-1';
                errorElement.textContent = 'The phone number must not exceed 11 digits.';
                
                // Insert after the phone input
                this.parentNode.insertBefore(errorElement, this.nextSibling);
            }
        });
        
        // Clear phone error when user starts typing
        phoneInput.addEventListener('input', function() {
            const phoneErrorId = 'phone-error';
            const errorElement = document.getElementById(phoneErrorId);
            if (errorElement) {
                errorElement.remove();
            }
        });
    }
});
</script>
@endsection 