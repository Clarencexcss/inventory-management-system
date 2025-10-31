<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - {{ $product->name ?? 'Product' }}</title>

  <!-- Bootstrap CSS -->
<link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

<!-- Font Awesome -->
<link href="{{ asset('css/fontawesome.min.css') }}" rel="stylesheet">

    
    <style>
        :root {
            --primary-color: #8B0000;
            --secondary-color: #4A0404;
            --accent-color: #FF4136;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f0f2f5;
            color: #333;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .card {
            border: none;
            box-shadow: var(--card-shadow);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
            font-weight: 600;
        }

        .product-image-container {
            position: relative;
            background-color: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
        }

        .product-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .price {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 15px 0;
        }

        .stock-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 1;
            font-weight: 600;
            padding: 8px 15px;
            border-radius: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .low-stock {
            background-color: #ffc107;
            color: #212529;
        }

        .out-of-stock {
            background-color: #dc3545;
            color: white;
        }

        .quantity-input {
            width: 100%;
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #ced4da;
            font-size: 1.1rem;
            text-align: center;
        }

        .quantity-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(139, 0, 0, 0.25);
        }

        .add-to-cart-btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 12px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .product-info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }

        .product-info-value {
            color: #212529;
            font-size: 1.1rem;
        }

        .related-product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }

        .related-product-title {
            font-weight: 600;
            font-size: 0.95rem;
            color: #212529;
            margin-bottom: 3px;
        }

        .related-product-price {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 0.9rem;
        }

        .view-details-btn {
            border-radius: 6px;
            font-size: 0.85rem;
            padding: 5px 10px;
            font-weight: 500;
        }

        .breadcrumb {
            background-color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
        }

        .product-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 20px 0;
        }

        .meta-item {
            flex: 1;
            min-width: 150px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            text-align: center;
        }

        .meta-label {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .meta-value {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .unit-info {
            background-color: #e9ecef;
            color: #495057;
            font-weight: 500;
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
        }

        .alert-info {
            border-radius: 8px;
            border: none;
            background-color: #d1ecf1;
        }

        .alert-warning {
            border-radius: 8px;
            border: none;
        }

        .section-title {
            color: var(--primary-color);
            font-weight: 700;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .related-products-container {
            max-height: 500px;
            overflow-y: auto;
        }

        /* Scrollbar styling */
        .related-products-container::-webkit-scrollbar {
            width: 6px;
        }

        .related-products-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .related-products-container::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
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

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('customer.products') }}">Products</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">{{ $product->name ?? 'Product' }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Product Details -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <!-- Product Image -->
                            <div class="col-md-6 mb-4">
                                <div class="product-image-container position-relative">
                                    @if($product->product_image)
                                        <img src="{{ asset('storage/products/' . $product->product_image) }}" 
                                             alt="{{ $product->name ?? 'Product' }}" class="product-image w-100">
                                    @else
                                        <div class="product-image d-flex align-items-center justify-content-center" style="height: 300px;">
                                            <i class="fas fa-image fa-4x text-muted"></i>
                                        </div>
                                    @endif
                                    
                                    @if($product->quantity <= 0)
                                        <span class="badge out-of-stock stock-badge">Out of Stock</span>
                                    @elseif($product->quantity <= 5)
                                        <span class="badge low-stock stock-badge">Low Stock</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Product Info -->
                            <div class="col-md-6">
                                <h2 class="mb-3">{{ $product->name ?? 'Unnamed Product' }}</h2>
                                
                                <div class="price">
                                    ₱{{ number_format($product->selling_price ?? 0, 2) }}
                                </div>
                                
                                <div class="alert alert-info py-2 px-3 mb-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-balance-scale me-2"></i>
                                        <span class="fw-bold">
                                            @if($product->unit && strtolower($product->unit->name) === 'kg')
                                                Sold per kilogram
                                            @elseif($product->unit && strtolower($product->unit->name) === 'piece')
                                                Sold per piece
                                            @elseif($product->unit && strtolower($product->unit->name) === 'package')
                                                Sold per package
                                            @elseif($product->unit && strtolower($product->unit->name) === 'box')
                                                Sold per box
                                            @elseif($product->unit && strtolower($product->unit->name) === 'dozen')
                                                Sold per dozen
                                            @else
                                                Sold per {{ $product->unit->name ?? 'unit' }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="product-meta">
                                    <div class="meta-item">
                                        <div class="meta-label">Product Code</div>
                                        <div class="meta-value">{{ $product->code ?? 'N/A' }}</div>
                                    </div>
                                    <div class="meta-item">
                                        <div class="meta-label">Category</div>
                                        <div class="meta-value">{{ $product->category->name ?? 'Uncategorized' }}</div>
                                    </div>
                                    <div class="meta-item">
                                        <div class="meta-label">Unit</div>
                                        <div class="meta-value">{{ $product->unit->name ?? 'kg' }}</div>
                                    </div>
                                    <div class="meta-item">
                                        <div class="meta-label">Stock</div>
                                        <div class="meta-value">{{ $product->quantity ?? 0 }}</div>
                                    </div>
                                </div>
                                
                                @if($product->notes)
                                    <div class="mb-4">
                                        <h5 class="section-title">Description</h5>
                                        <p class="text-muted">{{ $product->notes }}</p>
                                    </div>
                                @endif
                                
                                @if($product->quantity > 0)
                                    <form action="{{ route('customer.cart.add') }}" method="POST" class="mb-3">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <div class="row align-items-end">
                                            <div class="col-md-6 mb-3">
                                                <label for="quantity" class="form-label product-info-label">Quantity</label>
                                                <input type="number" class="quantity-input" 
                                                       id="quantity" name="quantity" value="1" 
                                                       min="1" max="{{ $product->quantity }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <button type="submit" class="btn btn-primary add-to-cart-btn w-100">
                                                    <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        This product is currently out of stock.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Related Products -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-thumbs-up me-2"></i>Related Products
                        </h5>
                    </div>
                    <div class="card-body related-products-container">
                        @if($relatedProducts->count() > 0)
                            @foreach($relatedProducts as $relatedProduct)
                                <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                                    <div class="flex-shrink-0 me-3">
                                        @if($relatedProduct->product_image)
                                            <img src="{{ asset('storage/products/' . $relatedProduct->product_image) }}" 
                                                 alt="{{ $relatedProduct->name ?? 'Related Product' }}" 
                                                 class="related-product-image">
                                        @else
                                            <div class="related-product-image d-flex align-items-center justify-content-center bg-light">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="related-product-title mb-1">{{ $relatedProduct->name ?? 'Related Product' }}</h6>
                                        <p class="related-product-price mb-2">
                                            ₱{{ number_format($relatedProduct->selling_price ?? 0, 2) }}
                                            @if($relatedProduct->unit)
                                                /{{ $relatedProduct->unit->name }}
                                            @else
                                                /kg
                                            @endif
                                        </p>
                                        <a href="{{ route('customer.products.show', $relatedProduct) }}" 
                                           class="btn btn-sm btn-outline-primary view-details-btn">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center py-4">No related products found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

</body>
</html>