@extends('layouts.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Create Customer') }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs')
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <form action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">
                                    {{ __('Customer Image') }}
                                </h3>

                                <img class="img-account-profile mb-2" src="{{ asset('assets/img/demo/user-placeholder.svg') }}" alt="" id="image-preview" />

                                <div class="small font-italic text-muted mb-2">JPG or PNG no larger than 2 MB</div>

                                <input class="form-control @error('photo') is-invalid @enderror" type="file"  id="image" name="photo" accept="image/*" onchange="previewImage();">

                                @error('photo')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">
                                    {{ __('Customer Details') }}
                                </h3>

                                <div class="row row-cards">
                                    <div class="col-md-12">
                                        <x-input name="name" :required="true" id="nameInput"/>
                                        <small class="form-text text-muted" id="nameHelp">Name may only contain letters, spaces, periods, hyphens, and apostrophes.</small>

                                        <x-input label="Email address" name="email" :required="true"/>
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <x-input label="Phone Number" name="phone" :required="true" placeholder="e.g., 09123456789 or +63123456789" id="phoneInput"/>
                                        <small class="form-text text-muted">Phone number must start with +63 and be exactly 11 digits. If you type "09", it will be automatically converted to "+63".</small>
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <label for="bank_name" class="form-label">
                                            Bank Name
                                        </label>

                                        <select class="form-select form-control-solid @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name">
                                            <option selected="" disabled="">Select a bank:</option>
                                            <option value="BRI" @if(old('bank_name') == 'BRI')selected="selected"@endif>BRI</option>
                                            <option value="BNI" @if(old('bank_name') == 'BNI')selected="selected"@endif>BNI</option>
                                            <option value="BCA" @if(old('bank_name') == 'BCA')selected="selected"@endif>BCA</option>
                                            <option value="BSI" @if(old('bank_name') == 'BSI')selected="selected"@endif>BSI</option>
                                            <option value="Mandiri" @if(old('bank_name') == 'Mandiri')selected="selected"@endif>Mandiri</option>
                                        </select>

                                        @error('bank_name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>


                                    <div class="col-sm-6 col-md-6">
                                        <x-input label="Account holder" name="account_holder" />
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <x-input label="Account number" name="account_number" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label required">
                                            Address
                                        </label>

                                        <textarea name="address"
                                                  id="address"
                                                  rows="3"
                                                  class="form-control form-control-solid @error('address') is-invalid @enderror"
                                            >{{ old('address') }}</textarea>

                                        @error('address')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <x-button.save type="submit">
                                    {{ __('Save') }}
                                </x-button.save>

                                <x-button.back route="{{ route('customers.index') }}">
                                    {{ __('Cancel') }}
                                </x-button.back>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@pushonce('page-scripts')
    <script src="{{ asset('assets/js/img-preview.js') }}"></script>
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
@endpushonce
