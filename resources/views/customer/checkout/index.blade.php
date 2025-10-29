<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Checkout</title>

    <!-- Fonts-Bootstrap -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #8B0000;
            --secondary-color: #4A0404;
            --accent-color: #FF4136;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
        }

        /* Navbar Styling */
        .navbar {
            background-color: #8B0000 !important;
        }

        .navbar-brand {
            font-weight: bold;
            color: #ffffff !important;
        }

        .navbar .nav-link,
        .navbar .dropdown-toggle {
            color: #ffffff !important;
        }

        .navbar .nav-link:hover,
        .navbar .dropdown-toggle:hover {
            color: #f1f1f1 !important;
        }

        /* Buttons and Cards */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
        }

        .checkout-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }

        .checkout-item:last-child {
            border-bottom: none;
        }

        .summary-card {
            position: sticky;
            top: 20px;
        }
        
        /* Static field styling */
        .static-field {
            padding: 0.375rem 0.75rem;
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            background-color: #f8f9fa;
            min-height: calc(1.5em + 0.75rem + 2px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('customer.dashboard') }}">
                <i class="fas fa-drumstick-bite me-2"></i>
                Yannis Meatshop - Customer Portal
            </a>

            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('customer.products') }}">
                    <i class="fas fa-store me-1"></i>Products
                </a>
                <a class="nav-link" href="{{ route('customer.cart') }}">
                    <i class="fas fa-shopping-cart me-1"></i>Cart
                    <span class="badge bg-danger ms-1">{{ \Gloudemans\Shoppingcart\Facades\Cart::instance('customer')->count() }}</span>
                </a>
               
                <a class="nav-link" href="{{ route('customer.orders') }}">
                    <i class="fas fa-shopping-bag me-1"></i>My Orders
                </a>
                  <!-- Livewire Notification Bell -->
                @livewire('customer-notification-navbar')
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>
                        {{ auth()->user()->name ?? 'Customer' }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('customer.profile') }}">
                            <i class="fas fa-user-edit me-2"></i>My Profile
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('customer.logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Checkout Form -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-credit-card me-2"></i>Checkout Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('customer.checkout.place-order') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Customer Info -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3"><i class="fas fa-user me-2"></i>Customer Information for this Order</h6>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Customer Account</label>
                                    <div class="static-field">
                                        {{ $customer->name }}
                                    </div>
                                    <input type="hidden" name="full_name" value="{{ $customer->name }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Receiver Name (Optional)</label>
                                    <input type="text" class="form-control @error('receiver_name') is-invalid @enderror" name="receiver_name" value="{{ old('receiver_name') }}" placeholder="Leave blank to use your name">
                                    @error('receiver_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="form-text text-muted">Specify a different name if sending to another person</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <div class="static-field">
                                        {{ $customer->email }}
                                    </div>
                                    <input type="hidden" name="email" value="{{ $customer->email }}">
                                </div>
                            </div>

                            <!-- Delivery Info -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3"><i class="fas fa-truck me-2"></i>Delivery Information</h6>
                                </div>
                                
                                <!-- Location Fields -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">City *</label>
                                    <div class="static-field @error('city') is-invalid @enderror">
                                        {{ old('city', 'Cabuyao') }}
                                    </div>
                                    <input type="hidden" name="city" value="{{ old('city', 'Cabuyao') }}">
                                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Postal Code *</label>
                                    <div class="static-field @error('postal_code') is-invalid @enderror">
                                        {{ old('postal_code', '4025') }}
                                    </div>
                                    <input type="hidden" name="postal_code" value="{{ old('postal_code', '4025') }}">
                                    @error('postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Barangay *</label>
                                    <select class="form-control @error('barangay') is-invalid @enderror" name="barangay">
                                        <option value="">Select Barangay</option>
                                        @foreach($barangays as $barangay)
                                            <option value="{{ $barangay }}" {{ old('barangay') == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
                                        @endforeach
                                    </select>
                                    @error('barangay')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Street Name *</label>
                                    <input type="text" class="form-control @error('street_name') is-invalid @enderror" name="street_name" value="{{ old('street_name') }}" placeholder="Enter street name">
                                    @error('street_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Building (Optional)</label>
                                    <input type="text" class="form-control @error('building') is-invalid @enderror" name="building" value="{{ old('building') }}" placeholder="Enter building name">
                                    @error('building')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">House No. (Optional)</label>
                                    <input type="text" class="form-control @error('house_no') is-invalid @enderror" name="house_no" value="{{ old('house_no') }}" placeholder="Enter house number">
                                    @error('house_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="col-12 mb-3 d-none">
                                    <label class="form-label">Delivery Address *</label>
                                    <textarea class="form-control @error('delivery_address') is-invalid @enderror" name="delivery_address" rows="3" placeholder="Enter your complete delivery address">{{ old('delivery_address', $customer->address) }}</textarea>
                                    @error('delivery_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Contact Phone *</label>
                                    <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" name="contact_phone" value="{{ old('contact_phone', $customer->phone) }}" placeholder="Enter your contact number">
                                    @error('contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3"><i class="fas fa-credit-card me-2"></i>Payment Method</h6>
                                </div>
                                <div class="col-12">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_type" id="cash" value="cash" checked>
                                        <label class="form-check-label" for="cash"><i class="fas fa-money-bill me-2"></i>Cash on Delivery</label>
                                    </div>

                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_type" id="gcash" value="gcash">
                                        <label class="form-check-label" for="gcash"><i class="fas fa-mobile-alt me-2"></i>GCash</label>
                                    </div>

                                    <!-- GCash Upload Section -->
                                    <div id="gcash-upload-section" class="mt-3 d-none">
                                        <label class="form-label">GCash Reference Number *</label>
                                        <input type="text" class="form-control mb-2" name="gcash_reference" placeholder="Enter GCash Reference Number">
                                        
                                        <label class="form-label">Upload GCash Receipt *</label>
                                        <input type="file" class="form-control" name="gcash_receipt" accept="image/*">
                                    </div>

                                    @error('payment_type')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label class="form-label">Delivery Notes (Optional)</label>
                                    <textarea class="form-control @error('delivery_notes') is-invalid @enderror" name="delivery_notes" rows="3" placeholder="Any special instructions for delivery">{{ old('delivery_notes') }}</textarea>
                                    @error('delivery_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('customer.cart') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Back to Cart
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check me-1"></i>Place Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="col-lg-4">
                <div class="card summary-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-receipt me-2"></i>Order Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($cartItems as $item)
                            <div class="checkout-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $item->name ?? 'Product' }}</h6>
                                        <small class="text-muted">{{ $item->qty }} x ₱{{ number_format($item->price, 2) }} per {{ $item->options->unit ?? 'kg' }}</small>
                                    </div>
                                    <div class="text-end"><strong>₱{{ number_format($item->subtotal, 2) }}</strong></div>
                                </div>
                            </div>
                        @endforeach

                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span><span>₱{{ number_format($cartSubtotal, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong><strong class="text-primary">₱{{ number_format($cartSubtotal, 2) }}</strong>
                        </div>

                        <!-- Shop Information -->
                        <div class="border-top pt-3 mt-3">
                            <h6 class="mb-2"><i class="fas fa-store me-2"></i>From Yannis Meat Shop</h6>
                            <small class="text-muted d-block mb-1">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                Katapatn Rd, 17, Cabuyao City, 4025 Laguna
                            </small>
                            <small class="text-muted d-block mb-1">
                                <i class="fas fa-phone me-2"></i>
                                +63 09082413347
                            </small>
                            <small class="text-muted d-block">
                                <i class="fas fa-envelope me-2"></i>
                                yannismeatshop@gmail.com
                            </small>
                        </div>

                        <div class="alert alert-warning mt-3"><small><i class="fas fa-exclamation-triangle me-1"></i><strong>Note:</strong> Orders are processed during business hours (8 AM - 6 PM)</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Local JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const gcashRadio = document.getElementById("gcash");
            const cashRadio = document.getElementById("cash");
            const gcashSection = document.getElementById("gcash-upload-section");

            function toggleGCashSection() {
                if (gcashRadio.checked) {
                    gcashSection.classList.remove("d-none");
                } else {
                    gcashSection.classList.add("d-none");
                }
            }

            toggleGCashSection();

            document.querySelectorAll("input[name='payment_type']").forEach(input => {
                input.addEventListener("change", toggleGCashSection);
            });
            
            // Phone validation for checkout form
            const contactPhoneInput = document.querySelector("input[name='contact_phone']");
            if (contactPhoneInput) {
                contactPhoneInput.addEventListener('blur', function() {
                    let phoneNumber = this.value.trim();
                    
                    // If the phone number starts with "09" and is 11 digits, convert to +63 format
                    if (phoneNumber.startsWith('09') && phoneNumber.length === 11) {
                        this.value = '+63' + phoneNumber.substring(1);
                    }
                    
                    // Check for invalid length and add visual feedback
                    const phoneErrorId = 'phone-error-checkout';
                    let errorElement = document.getElementById(phoneErrorId);
                    
                    // Remove existing error message
                    if (errorElement) {
                        errorElement.remove();
                    }
                    
                    // Add error message if phone number is invalid
                    if (phoneNumber !== '' && !phoneNumber.match(/^(\+63\d{10}|09\d{9})$/)) {
                        // Create error message element
                        errorElement = document.createElement('div');
                        errorElement.id = phoneErrorId;
                        errorElement.className = 'text-danger mt-1';
                        errorElement.textContent = 'The contact phone must be a valid Philippine phone number (either 09xxxxxxxxx or +63xxxxxxxxxx format).';
                        
                        // Insert after the phone input
                        this.parentNode.insertBefore(errorElement, this.nextSibling);
                    }
                });
                
                // Clear phone error when user starts typing
                contactPhoneInput.addEventListener('input', function() {
                    const phoneErrorId = 'phone-error-checkout';
                    const errorElement = document.getElementById(phoneErrorId);
                    if (errorElement) {
                        errorElement.remove();
                    }
                });
            }
        });
    </script>
</body>
</html>