@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">

        {{-- Order Header --}}
        <div class="mb-4">
            <h1 class="page-title">{{ __('Order Details') }}</h1>
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
                                Approve Order
                            </button>
                        </form>
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
                                <img src="{{ asset('storage/' . $order->proof_of_payment) }}" alt="Proof of Payment" class="img-fluid" style="max-height: 200px;">
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
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
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
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->unitcost, 2) }}</td>
                                <td class="text-end">{{ number_format($item->total, 2) }}</td>
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
@endsection
