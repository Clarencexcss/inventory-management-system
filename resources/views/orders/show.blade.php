@extends('layouts.butcher')

@push('page-styles')
<style>
    .cursor-pointer:hover {
        opacity: 0.8;
        transform: scale(1.02);
        transition: all 0.2s ease-in-out;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
</style>
@endpush

@section('content')
<div class="page-body">
    <div class="container-xl">

        {{-- Order Header --}}
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="page-title">{{ __('Order Details') }}</h1>
                <x-back-button url="{{ route('orders.index') }}" text="Back to Orders" />
            </div>
            <p class="text-muted">Invoice: <strong>{{ $order->invoice_no }}</strong> | Customer: <strong>{{ $order->customer->name }}</strong></p>
        </div>

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">{{ __('Order Summary') }}</h3>
                <div class="d-flex gap-2">
                    @if ($order->order_status === \App\Enums\OrderStatus::PENDING)
                        <form action="{{ route('orders.update', $order) }}" method="POST">
                            @csrf
                            @method('put')
                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this order?')">
                                Approve Orders
                            </button>
                        </form>
                        
                        <!-- Cancel Order Button -->
                        <button type="button" 
                                class="btn btn-warning btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#cancelOrderModal">
                            <i class="ti ti-x me-1"></i>Cancel Order
                        </button>
                    @endif
                    <x-action.close route="{{ route('orders.index') }}"/>
                </div>
            </div>

            <div class="card-body">

                {{-- Basic Order Info --}}
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('Order Date') }}</label>
                        <input type="text" class="form-control" value="{{ $order->order_date->format('d-m-Y') }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('Payment Type') }}</label>
                        <input type="text" class="form-control" value="{{ $order->payment_type }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('Status') }}</label>
                        <span class="badge bg-{{ $order->order_status === \App\Enums\OrderStatus::PENDING ? 'warning' : 'success' }}">
                        {{ ucfirst($order->order_status->value) }}


                        </span>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('Customer') }}</label>
                        <input type="text" class="form-control" value="{{ $order->customer->name }}" disabled>
                    </div>
                </div>

                {{-- Delivery Info --}}
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Delivery Address') }}</label>
                        <input type="text" class="form-control" value="{{ $order->delivery_address }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('Contact Number') }}</label>
                        <input type="text" class="form-control" value="{{ $order->contact_phone }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('Delivery Notes') }}</label>
                        <textarea class="form-control" rows="2" disabled>{{ $order->delivery_notes }}</textarea>
                    </div>
                </div>

                {{-- GCash Info --}}
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('GCash Reference') }}</label>
                        <input type="text" class="form-control" value="{{ $order->gcash_reference }}" disabled>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">{{ __('Proof of Payment') }}</label>
                        <div class="border p-2 rounded bg-light text-center">
                            @if ($order->proof_of_payment)
                                <img src="{{ asset('storage/' . $order->proof_of_payment) }}" 
                                     alt="Proof of Payment" 
                                     class="img-fluid cursor-pointer" 
                                     style="max-height: 200px; cursor: pointer;" 
                                     data-bs-toggle="modal" 
                                     data-bs-target="#proofOfPaymentModal"
                                     title="Click to view full image">
                                <div class="mt-2">
                                    <small class="text-muted"><i class="ti ti-click"></i> Click image to enlarge</small>
                                </div>
                            @else
                                <span class="text-muted">{{ __('No image uploaded') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Products Table --}}
                <div class="table-responsive mb-4">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Photo</th>
                                <th>Product Name</th>
                                <th>Code</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->details as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">
                                    <img src="{{ $item->product->product_image ? asset('storage/products/'.$item->product->product_image) : asset('assets/img/products/default.webp') }}" class="img-thumbnail" style="max-height: 80px;">
                                </td>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->product->code }}</td>
                                <td class="text-center">{{ $item->quantity }} {{ $item->product->unit->name ?? 'kg' }}</td>
                                <td class="text-end">₱{{ number_format($item->unitcost, 2) }}/{{ $item->product->unit->name ?? 'kg' }}</td>
                                <td class="text-end">₱{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="6" class="text-end fw-bold">Paid Amount</td>
                                <td class="text-end fw-bold">{{ number_format($order->pay, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end fw-bold">Due</td>
                                <td class="text-end fw-bold">{{ number_format($order->due, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end fw-bold">VAT</td>
                                <td class="text-end fw-bold">{{ number_format($order->vat, 2) }}</td>
                            </tr>
                            <tr class="table-primary">
                                <td colspan="6" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold">{{ number_format($order->total, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            {{-- Footer Action --}}
            @if ($order->order_status === \App\Enums\OrderStatus::PENDING)
            <div class="card-footer text-end">
                <form action="{{ route('orders.update', $order) }}" method="POST">
                    @method('put')
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to complete this order?')">
                        Complete Order
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Cancel Order Modal --}}
@if ($order->order_status === \App\Enums\OrderStatus::PENDING)
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="cancelOrderModalLabel">
                    <i class="ti ti-alert-triangle me-2"></i>
                    Cancel Order #{{ $order->invoice_no }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('orders.cancel', $order) }}" method="POST" id="cancelOrderForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning border-0 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-alert-triangle fs-1 me-3"></i>
                            <div>
                                <h6 class="alert-heading mb-1">Important Warning</h6>
                                <p class="mb-0">Are you sure you want to cancel this order? This action cannot be undone and will:</p>
                                <ul class="mb-0 mt-2">
                                    <li>Restore product quantities to inventory</li>
                                    <li>Notify the customer about the cancellation</li>
                                    <li>Update the order status permanently</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Customer:</strong> {{ $order->customer->name }}
                        </div>
                        <div class="col-md-6">
                            <strong>Order Date:</strong> {{ $order->order_date->format('M d, Y') }}
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Payment Type:</strong> {{ ucfirst($order->payment_type) }}
                        </div>
                        <div class="col-md-6">
                            <strong>Total Amount:</strong> ₱{{ number_format($order->total, 2) }}
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label fw-bold">
                            Cancellation Reason <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" 
                                  id="cancellation_reason" 
                                  name="cancellation_reason" 
                                  rows="4" 
                                  required 
                                  placeholder="Please provide a detailed reason for cancelling this order. This will be sent to the customer..."></textarea>
                        <div class="form-text">This reason will be visible to the customer in their notification.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>Close
                    </button>
                    <button type="submit" class="btn btn-danger" id="confirmCancel">
                        <i class="ti ti-ban me-1"></i>Cancel Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('page-scripts')
<script>
// Prevent modal conflicts and improve UX
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('cancelOrderModal');
    if (modal) {
        // Clear form when modal is hidden
        modal.addEventListener('hidden.bs.modal', function() {
            const form = modal.querySelector('form');
            if (form) {
                form.reset();
                const textarea = form.querySelector('textarea[name="cancellation_reason"]');
                if (textarea) {
                    textarea.classList.remove('is-invalid');
                }
            }
        });
        
        // Add confirmation before form submission
        const confirmButton = document.getElementById('confirmCancel');
        if (confirmButton) {
            confirmButton.addEventListener('click', function(e) {
                const textarea = document.getElementById('cancellation_reason');
                if (!textarea.value.trim()) {
                    e.preventDefault();
                    textarea.focus();
                    textarea.classList.add('is-invalid');
                    return false;
                }
                textarea.classList.remove('is-invalid');
            });
        }
    }
});
</script>
@endpush
@endif

{{-- Proof of Payment Modal --}}
@if ($order->proof_of_payment)
<div class="modal fade" id="proofOfPaymentModal" tabindex="-1" aria-labelledby="proofOfPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="proofOfPaymentModalLabel">
                    <i class="ti ti-photo me-2"></i>Proof of Payment - Order #{{ $order->invoice_no }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4" style="background-color: #f8f9fa;">
                <img src="{{ asset('storage/' . $order->proof_of_payment) }}" 
                     alt="Proof of Payment" 
                     class="img-fluid rounded shadow" 
                     style="max-width: 100%; max-height: 80vh; object-fit: contain;">
                <div class="mt-3">
                    <p class="text-muted mb-1"><strong>GCash Reference:</strong> {{ $order->gcash_reference ?? 'N/A' }}</p>
                    <p class="text-muted mb-0"><strong>Order Date:</strong> {{ $order->order_date->format('M d, Y') }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ asset('storage/' . $order->proof_of_payment) }}" 
                   target="_blank" 
                   class="btn btn-primary">
                    <i class="ti ti-external-link me-1"></i>Open in New Tab
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
