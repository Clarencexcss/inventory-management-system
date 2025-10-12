<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - My Profile</title>

    <!-- Local CSS -->
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
        
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }
        
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
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
        <div class="mb-3">
            <x-back-button url="{{ route('customer.dashboard') }}" text="Back to Dashboard" />
        </div>
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
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-edit me-2"></i>My Profile
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Profile Form Start -->
                        <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="name">Full Name</label>
                                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="username">Username</label>
                                <input type="text" name="username" value="{{ old('username', auth()->user()->username) }}" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label for="phone">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label for="email">Email <small>(optional - leave blank to keep current)</small></label>
                                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label for="address">Address</label>
                                <textarea name="address" class="form-control" rows="3">{{ old('address', auth()->user()->address) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="password">New Password <small>(optional - leave blank to keep current)</small></label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label for="photo">Profile Photo</label>
                                <input type="file" name="photo" class="form-control">
                                @if(auth()->user()->photo)
                                    <img src="{{ asset('storage/customers/' . auth()->user()->photo) }}" width="100" class="mt-2 rounded">
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                        <!-- Profile Form End -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Local JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
</body>
</html>
