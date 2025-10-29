
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Edit Customer') }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs', ['model' => $customer])
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <form action="{{ route('customers.update', $customer) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('put')
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">
                                    {{ __('Profile Image') }}
                                </h3>

                                <img
                                    class="img-account-profile mb-2"
                                    src="{{ $customer->photo ? asset('storage/customers/'.$customer->photo) : asset('assets/img/demo/user-placeholder.svg') }}"
                                    id="image-preview"
                                />

                                <div class="small font-italic text-muted mb-2">JPG or PNG no larger than 2 MB</div>

                                <input class="form-control @error('photo') is-invalid @enderror" type="file" id="image" name="photo" accept="image/*" onchange="previewImage();">

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
                                        <x-input name="name" :value="old('name', $customer->name)" :required="true" id="nameInput" />
                                        <small class="form-text text-muted" id="nameHelp">Name may only contain letters, spaces, periods, hyphens, and apostrophes.</small>

                                        <x-input label="Email address" name="email" :value="old('email', $customer->email)" :required="true" />
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <x-input label="Phone number" name="phone" :value="old('phone', $customer->phone)" :required="true" placeholder="e.g., 09123456789 or +63123456789" id="phoneInput" />
                                        <small class="form-text text-muted">Phone number must start with +63 and be exactly 11 digits. If you type "09", it will be automatically converted to "+63".</small>
                                    </div>

                                    <!-- Password Fields -->
                                    <div class="col-sm-6 col-md-6">
                                        <x-input type="password" label="New Password (optional)" name="password" />
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <x-input type="password" label="Confirm Password" name="password_confirmation" />
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="address" class="form-label required">
                                                {{ __('Address') }}
                                            </label>

                                            <textarea
                                                id="address"
                                                name="address"
                                                rows="3"
                                                class="form-control @error('address') is-invalid @enderror"
                                            >{{ old('address', $customer->address) }}</textarea>

                                            @error('address')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer text-end">
                                <x-button.save type="submit">
                                    {{ __('Update') }}
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
