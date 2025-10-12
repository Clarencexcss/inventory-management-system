<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">
                {{ __('Orders') }}
            </h3>
        </div>

        <div class="card-actions">
            <x-action.create route="{{ route('orders.create') }}" />
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <div class="d-flex">
            <div class="text-secondary">
                Show
                <div class="mx-2 d-inline-block">
                    <select wire:model.live="perPage" class="form-select form-select-sm" aria-label="result per page">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                    </select>
                </div>
                entries
            </div>
            <div class="ms-auto text-secondary">
                Search:
                <div class="ms-2 d-inline-block">
                    <input type="text" wire:model.live="search" class="form-control form-control-sm" aria-label="Search invoice">
                </div>
            </div>
        </div>
    </div>

    <x-spinner.loading-spinner/>

    <div class="table-responsive" wire:loading.remove>
        @php
            $pendingOrders = $orders->where('order_status', \App\Enums\OrderStatus::PENDING);
            $completeOrders = $orders->where('order_status', \App\Enums\OrderStatus::COMPLETE);
            $cancelledOrders = $orders->where('order_status', \App\Enums\OrderStatus::CANCELLED);
        @endphp

        @if($orders->count() > 0)
            {{-- Pending Orders Section --}}
            @if($pendingOrders->count() > 0)
                <div class="mb-4">
                    <div class="bg-warning bg-opacity-10 border-start border-warning border-4 p-3 mb-3">
                        <h5 class="mb-0 text-warning">
                            <i class="ti ti-clock me-2"></i>
                            Pending Orders ({{ $pendingOrders->count() }})
                        </h5>
                    </div>
                    <table class="table table-bordered card-table table-vcenter text-nowrap">
                        <thead class="table-warning">
                            <tr>
                                <th class="align-middle text-center w-1">{{ __('No.') }}</th>
                                <th class="align-middle text-center">{{ __('Invoice No.') }}</th>
                                <th class="align-middle text-center">{{ __('Customer') }}</th>
                                <th class="align-middle text-center">{{ __('Date') }}</th>
                                <th class="align-middle text-center">{{ __('Payment') }}</th>
                                <th class="align-middle text-center">{{ __('Total') }}</th>
                                <th class="align-middle text-center">{{ __('Status') }}</th>
                                <th class="align-middle text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingOrders as $order)
                                <tr>
                                    <td class="align-middle text-center">{{ $loop->iteration }}</td>
                                    <td class="align-middle text-center">{{ $order->invoice_no }}</td>
                                    <td class="align-middle">{{ $order->customer->name }}</td>
                                    <td class="align-middle text-center">{{ $order->order_date->format('d-m-Y') }}</td>
                                    <td class="align-middle text-center">{{ $order->payment_type }}</td>
                                    <td class="align-middle text-center">₱{{ number_format($order->total, 2) }}</td>
                                    <td class="align-middle text-center">
                                        <x-status dot color="orange" class="text-uppercase">
                                            {{ $order->order_status->label() }}
                                        </x-status>
                                    </td>
                                    <td class="align-middle text-center" style="width: 5%">
                                        <x-button.show class="btn-icon" route="{{ route('orders.show', $order) }}"/>
                                        <x-button.print class="btn-icon" route="{{ route('order.downloadInvoice', $order) }}"/>
                                        
                                        <!-- Cancel Order Button -->
                                        <button type="button" class="btn btn-sm btn-outline-warning me-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#cancelOrderModal{{ $order->id }}"
                                                title="Cancel Order">
                                            <i class="ti ti-x me-1"></i>Cancel
                                        </button>
                                        
                                        <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="ti ti-trash me-1"></i>Delete
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Complete Orders Section --}}
            @if($completeOrders->count() > 0)
                <div class="mb-4">
                    <div class="bg-success bg-opacity-10 border-start border-success border-4 p-3 mb-3">
                        <h5 class="mb-0 text-success">
                            <i class="ti ti-check me-2"></i>
                            Complete Orders ({{ $completeOrders->count() }})
                        </h5>
                    </div>
                    <table class="table table-bordered card-table table-vcenter text-nowrap">
                        <thead class="table-success">
                            <tr>
                                <th class="align-middle text-center w-1">{{ __('No.') }}</th>
                                <th class="align-middle text-center">{{ __('Invoice No.') }}</th>
                                <th class="align-middle text-center">{{ __('Customer') }}</th>
                                <th class="align-middle text-center">{{ __('Date') }}</th>
                                <th class="align-middle text-center">{{ __('Payment') }}</th>
                                <th class="align-middle text-center">{{ __('Total') }}</th>
                                <th class="align-middle text-center">{{ __('Status') }}</th>
                                <th class="align-middle text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completeOrders as $order)
                                <tr>
                                    <td class="align-middle text-center">{{ $loop->iteration }}</td>
                                    <td class="align-middle text-center">{{ $order->invoice_no }}</td>
                                    <td class="align-middle">{{ $order->customer->name }}</td>
                                    <td class="align-middle text-center">{{ $order->order_date->format('d-m-Y') }}</td>
                                    <td class="align-middle text-center">{{ $order->payment_type }}</td>
                                    <td class="align-middle text-center">₱{{ number_format($order->total, 2) }}</td>
                                    <td class="align-middle text-center">
                                        <x-status dot color="green" class="text-uppercase">
                                            {{ $order->order_status->label() }}
                                        </x-status>
                                    </td>
                                    <td class="align-middle text-center" style="width: 5%">
                                        <x-button.show class="btn-icon" route="{{ route('orders.show', $order) }}"/>
                                        <x-button.print class="btn-icon" route="{{ route('order.downloadInvoice', $order) }}"/>
                                        <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-outline-danger" title="Delete">
                                                <i class="ti ti-trash"></i>Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Cancelled Orders Section --}}
            @if($cancelledOrders->count() > 0)
                <div class="mb-4">
                    <div class="bg-danger bg-opacity-10 border-start border-danger border-4 p-3 mb-3">
                        <h5 class="mb-0 text-danger">
                            <i class="ti ti-x me-2"></i>
                            Cancelled Orders ({{ $cancelledOrders->count() }})
                        </h5>
                    </div>
                    <table class="table table-bordered card-table table-vcenter text-nowrap">
                        <thead class="table-danger">
                            <tr>
                                <th class="align-middle text-center w-1">{{ __('No.') }}</th>
                                <th class="align-middle text-center">{{ __('Invoice No.') }}</th>
                                <th class="align-middle text-center">{{ __('Customer') }}</th>
                                <th class="align-middle text-center">{{ __('Date') }}</th>
                                <th class="align-middle text-center">{{ __('Payment') }}</th>
                                <th class="align-middle text-center">{{ __('Total') }}</th>
                                <th class="align-middle text-center">{{ __('Cancel Reason') }}</th>
                                <th class="align-middle text-center">{{ __('Status') }}</th>
                                <th class="align-middle text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cancelledOrders as $order)
                                <tr>
                                    <td class="align-middle text-center">{{ $loop->iteration }}</td>
                                    <td class="align-middle text-center">{{ $order->invoice_no }}</td>
                                    <td class="align-middle">{{ $order->customer->name }}</td>
                                    <td class="align-middle text-center">{{ $order->order_date->format('d-m-Y') }}</td>
                                    <td class="align-middle text-center">{{ $order->payment_type }}</td>
                                    <td class="align-middle text-center">₱{{ number_format($order->total, 2) }}</td>
                                    <td class="align-middle text-center">
                                        <span class="text-danger">
                                            {{ $order->cancellation_reason ?? 'No reason provided' }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <x-status dot color="red" class="text-uppercase">
                                            {{ $order->order_status->label() }}
                                        </x-status>
                                    </td>
                                    <td class="align-middle text-center" style="width: 5%">
                                        <x-button.show class="btn-icon" route="{{ route('orders.show', $order) }}"/>
                                        <x-button.print class="btn-icon" route="{{ route('order.downloadInvoice', $order) }}"/>
                                        <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-outline-danger" title="Delete">
                                                <i class="ti ti-trash"></i>Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <div class="empty">
                    <div class="empty-icon">
                        <i class="ti ti-package-off" style="font-size: 3rem; color: #6c757d;"></i>
                    </div>
                    <p class="empty-title">No orders found</p>
                    <p class="empty-subtitle text-muted">
                        Try adjusting your search or filter to find what you're looking for.
                    </p>
                </div>
            </div>
        @endif
    </div>

    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">
            Showing <span>{{ $orders->firstItem() }}</span> to <span>{{ $orders->lastItem() }}</span> of <span>{{ $orders->total() }}</span> entries
        </p>

        <ul class="pagination m-0 ms-auto">
            {{ $orders->links() }}
        </ul>
    </div>

    {{-- Cancel Order Modals --}}
    @foreach($pendingOrders as $order)
    <div class="modal fade" id="cancelOrderModal{{ $order->id }}" tabindex="-1" aria-labelledby="cancelOrderModalLabel{{ $order->id }}" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="cancelOrderModalLabel{{ $order->id }}">
                        <i class="ti ti-alert-triangle me-2"></i>
                        Cancel Order #{{ $order->invoice_no }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('orders.cancel', $order) }}" method="POST" id="cancelOrderForm{{ $order->id }}">
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
                            <label for="cancellation_reason{{ $order->id }}" class="form-label fw-bold">
                                Cancellation Reason <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" 
                                      id="cancellation_reason{{ $order->id }}" 
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
                        <button type="submit" class="btn btn-danger" id="confirmCancel{{ $order->id }}">
                            <i class="ti ti-ban me-1"></i>Cancel Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

<script>
// Prevent modal conflicts and improve UX
document.addEventListener('DOMContentLoaded', function() {
    // Clear form when modal is hidden
    document.querySelectorAll('[id^="cancelOrderModal"]').forEach(function(modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            const form = modal.querySelector('form');
            if (form) {
                form.reset();
            }
        });
    });
    
    // Add confirmation before form submission
    document.querySelectorAll('[id^="confirmCancel"]').forEach(function(button) {
        button.addEventListener('click', function(e) {
            const textarea = button.closest('form').querySelector('textarea[name="cancellation_reason"]');
            if (!textarea.value.trim()) {
                e.preventDefault();
                textarea.focus();
                textarea.classList.add('is-invalid');
                return false;
            }
            textarea.classList.remove('is-invalid');
        });
    });
});
</script>
