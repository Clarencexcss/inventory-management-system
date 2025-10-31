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
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --card-hover-shadow: 0 10px 15px rgba(0, 0, 0, 0.15);
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f0f2f5;
            color: #333;
        }

        /* Navbar */
        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: bold;
            color: #ffffff !important;
        }

        .navbar-nav .nav-link {
            color: #f1f1f1 !important;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none !important;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: #ffffff !important;
            transform: translateY(-1px);
            text-decoration: none !important;
        }

        /* Buttons */
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

        .btn-back {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-print {
            border-radius: 8px;
            font-weight: 600;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .btn-print:hover {
            transform: translateY(-2px);
        }

        .btn-cancel {
            border-radius: 8px;
            font-weight: 600;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            transform: translateY(-2px);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .card:hover {
            box-shadow: var(--card-hover-shadow);
        }

        .card-header {
            background-color: var(--primary-color);
            color: #fff;
            border-bottom: none;
            font-weight: 600;
            padding: 1rem 1.5rem;
        }

        .info-box {
            background-color: white;
            border: 1px solid #e9ecef;
            border-left: 4px solid var(--primary-color);
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .info-box:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
            font-weight: 500;
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
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .status-badge {
            font-size: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-complete {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
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
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Section Headers */
        .section-header {
            color: var(--primary-color);
            font-weight: 700;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin: 25px 0 15px 0;
        }

        /* Modal */
        .modal-header {
            background-color: var(--primary-color);
            color: white;
        }

        .modal-content {
            border-radius: 12px;
            overflow: hidden;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .product-img {
                width: 50px;
                height: 50px;
            }
            
            .info-box {
                padding: 0.75rem;
            }
            
            .info-label {
                font-size: 0.75rem;
            }
            
            .info-value {
                font-size: 0.9rem;
            }
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
            <div>
                <h5 class="mb-0 d-inline-block me-3"><i class="fas fa-receipt me-2"></i>Order Details</h5>
                <a href="{{ route('customer.orders.download-invoice', $order->id) }}" class="btn btn-print btn-primary" target="_blank">
                    <i class="fas fa-print me-1"></i> Print Invoice
                </a>
            </div>
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
        <div class="card">
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
                    
                    <!-- Customer Name -->
                    <div class="col-md-3 mb-3">
                        <div class="info-box">
                            <div class="info-label">Customer Account</div>
                            <div class="info-value">{{ $order->customer_name }}</div>
                        </div>
                    </div>
                    
                    @if($order->receiver_name)
                    <!-- Receiver Name -->
                    <div class="col-md-3 mb-3">
                        <div class="info-box">
                            <div class="info-label">Receiver Name</div>
                            <div class="info-value">{{ $order->receiver_name }}</div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Customer Email -->
                    <div class="col-md-3 mb-3">
                        <div class="info-box">
                            <div class="info-label">Customer Email</div>
                            <div class="info-value">{{ $order->customer_email }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Delivery Information -->
                <h5 class="section-header">Delivery Information</h5>
                
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="info-box">
                            <div class="info-label">City</div>
                            <div class="info-value">{{ $order->city }}</div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="info-box">
                            <div class="info-label">Postal Code</div>
                            <div class="info-value">{{ $order->postal_code }}</div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="info-box">
                            <div class="info-label">Barangay</div>
                            <div class="info-value">{{ $order->barangay }}</div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="info-box">
                            <div class="info-label">Street Name</div>
                            <div class="info-value">{{ $order->street_name }}</div>
                        </div>
                    </div>
                    
                    @if($order->building)
                    <div class="col-md-3 mb-3">
                        <div class="info-box">
                            <div class="info-label">Building</div>
                            <div class="info-value">{{ $order->building }}</div>
                        </div>
                    </div>
                    @endif
                    
                    @if($order->house_no)
                    <div class="col-md-3 mb-3">
                        <div class="info-box">
                            <div class="info-label">House No.</div>
                            <div class="info-value">{{ $order->house_no }}</div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="col-md-6 mb-3">
                        <div class="info-box">
                            <div class="info-label">Full Delivery Address</div>
                            <div class="info-value">{{ $order->delivery_address }}</div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="info-box">
                            <div class="info-label">Contact Number</div>
                            <div class="info-value">{{ $order->contact_phone }}</div>
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
                <h5 class="section-header">Proof of Payment</h5>
                
                <div class="row">
                    <div class="col-12">
                        <div class="text-center p-4 bg-light rounded">
                            <img src="{{ asset('storage/' . $order->proof_of_payment) }}" alt="Proof of Payment" class="proof-image">
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Shop Information -->
                <h5 class="section-header">From Yannis Meat Shop</h5>
                
                <div class="row">
                    <div class="col-12">
                        <div class="info-box">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-label">Address</div>
                                    <div class="info-value">Katapatn Rd, 17, Cabuyao City, 4025 Laguna</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">Phone</div>
                                    <div class="info-value">+63 09082413347</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">Email</div>
                                    <div class="info-value">yannismeatshop@gmail.com</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                                        <div class="product-img d-flex align-items-center justify-content-center bg-light">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
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
                            <tr class="grand-total-row">
                                <td colspan="4" class="text-end">Grand Total:</td>
                                <td class="text-end">₱{{ number_format($order->details->sum('total'), 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Cancel Order Button (Only for Pending Orders) -->
                @if($order->order_status === \App\Enums\OrderStatus::PENDING)
                <div class="text-end mt-4">
                    <button type="button" class="btn btn-cancel btn-danger" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                        <i class="fas fa-times me-1"></i>Cancel This Order
                    </button>
                </div>

                <!-- Cancel Modal -->
                <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
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