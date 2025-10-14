<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Order Details</title>

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    @livewireStyles

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

        /* Navbar */
        .navbar {
            background-color: var(--primary-color) !important;
        }

        .navbar-brand {
            font-weight: bold;
            color: #ffffff !important;
        }

        .navbar-nav .nav-link {
            color: #f1f1f1 !important;
            font-weight: 500;
            text-decoration: none !important;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: #ffffff !important;
            text-decoration: none !important;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 50px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            color: white;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .card-header {
            background-color: var(--primary-color);
            color: #fff;
            border-bottom: none;
            padding: 1rem 1.5rem;
        }

        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid var(--primary-color);
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
        }

        .info-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.85rem;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }

        .info-value {
            color: #333;
            font-size: 1rem;
        }

        /* Table */
        .table th {
            background-color: #f1f1f1;
            color: #333;
            font-weight: 600;
        }

        .table-bordered {
            border: 1px solid #dee2e6;
        }

        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.5rem;
        }

        .status-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
        }

        .status-pending {
            background-color: #ffc107;
            color: #000;
        }

        .status-complete {
            background-color: #28a745;
            color: #fff;
        }

        .status-cancelled {
            background-color: #dc3545;
            color: #fff;
        }

        .total-row {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .grand-total-row {
            background-color: var(--primary-color);
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .proof-image {
            max-width: 100%;
            max-height: 400px;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body>
    <!-- Navbar -->
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
                <a class="nav-link active" href="{{ route('customer.orders') }}">
                    <i class="fas fa-shopping-bag me-1"></i>My Orders
                </a>

                @livewire('customer-notification-navbar')

                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>
                        {{ auth()->user()->name ?? 'Customer' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}"><i class="fas fa-home me-2"></i>Dashboard</a></li>
                        <li><a class="dropdown-item" href="{{ route('customer.profile') }}"><i class="fas fa-user-edit me-2"></i>My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('customer.logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4 mb-5">
        <!-- Back Button and Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('customer.orders') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to My Orders
            </a>
            <h5 class="mb-0"><i class="fas fa-receipt me-2 text-danger"></i>Order Details</h5>
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

        <!-- Order Information Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="mb-0">Order #{{ $order->id }} - Invoice: {{ $order->invoice_no ?? 'N/A' }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Order Date -->
                    <div class="col-md-3 mb-3">
                        <div class="info-box">
                            <div class="info-label">Order Date</div>
                            <div class="info-value">{{ $order->created_at->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                        </div>
                    </div>

                    <!-- Order Status -->
                    <div class="col-md-3 mb-3">
                        <div class="info-box">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                @php
                                    $statusClass = match($order->order_status) {
                                        \App\Enums\OrderStatus::COMPLETE => 'status-complete',
                                        \App\Enums\OrderStatus::CANCELLED => 'status-cancelled',
                                        default => 'status-pending'
                                    };
                                @endphp
                                <span class="badge status-badge {{ $statusClass }}">
                                    {{ $order->order_status instanceof \App\Enums\OrderStatus ? $order->order_status->label() : ucfirst($order->order_status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Type -->
                    <div class="col-md-3 mb-3">
                        <div class="info-box">
                            <div class="info-label">Payment Method</div>
                            <div class="info-value">{{ ucfirst($order->payment_type ?? 'N/A') }}</div>
                        </div>
                    </div>

                    <!-- Total Amount -->
                    <div class="col-md-3 mb-3">
                        <div class="info-box">
                            <div class="info-label">Total Amount</div>
                            <div class="info-value text-danger fw-bold">₱{{ number_format($order->details->sum('total'), 2) }}</div>
                        </div>
                    </div>
                </div>

                <!-- GCash Reference (if applicable) -->
                @if($order->gcash_reference)
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="info-box">
                            <div class="info-label">GCash Reference Number</div>
                            <div class="info-value">{{ $order->gcash_reference }}</div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Proof of Payment -->
                @if($order->proof_of_payment)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="info-label mb-2">Proof of Payment</div>
                        <div class="text-center p-3 bg-light rounded">
                            <img src="{{ asset('storage/' . $order->proof_of_payment) }}" alt="Proof of Payment" class="proof-image">
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Order Items Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Order Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 80px;">Image</th>
                                <th>Product Name</th>
                                <th class="text-center" style="width: 100px;">Quantity</th>
                                <th class="text-end" style="width: 150px;">Unit Price</th>
                                <th class="text-end" style="width: 150px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->details as $detail)
                            <tr>
                                <td class="text-center">
                                    @if($detail->product && $detail->product->product_image)
                                        <img src="{{ asset('storage/products/' . $detail->product->product_image) }}" alt="{{ $detail->product->name }}" class="product-img">
                                    @else
                                        <img src="{{ asset('assets/img/products/default.webp') }}" alt="Product" class="product-img">
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $detail->product->name ?? 'Product' }}</strong><br>
                                    <small class="text-muted">Code: {{ $detail->product->code ?? 'N/A' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $detail->quantity }}</span>
                                    {{ $detail->product->unit->name ?? 'pcs' }}
                                </td>
                                <td class="text-end">₱{{ number_format($detail->unitcost, 2) }}</td>
                                <td class="text-end fw-bold">₱{{ number_format($detail->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="4" class="text-end">Subtotal:</td>
                                <td class="text-end">₱{{ number_format($order->details->sum('total'), 2) }}</td>
                            </tr>
                            @if($order->vat)
                            <tr class="total-row">
                                <td colspan="4" class="text-end">VAT (12%):</td>
                                <td class="text-end">₱{{ number_format($order->vat, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="grand-total-row">
                                <td colspan="4" class="text-end">Grand Total:</td>
                                <td class="text-end">₱{{ number_format($order->details->sum('total') + ($order->vat ?? 0), 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Cancel Order Button (Only for Pending Orders) -->
                @if($order->order_status === \App\Enums\OrderStatus::PENDING)
                <div class="text-end mt-3">
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                        <i class="fas fa-times me-1"></i>Cancel This Order
                    </button>
                </div>

                <!-- Cancel Modal -->
                <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                                <h5 class="modal-title">Cancel Order #{{ $order->id }}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Are you sure you want to cancel this order? This action cannot be undone.
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Reason for cancellation <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="reason" rows="3" required placeholder="Please provide a reason..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-danger">Cancel Order</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    @livewireScripts
</body>
</html>
