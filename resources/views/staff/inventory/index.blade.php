@extends('layouts.butcher')

@push('page-styles')
<style>
    .quick-action-card {
        border-left: 4px solid var(--primary-color);
        transition: transform 0.2s, box-shadow 0.2s;
        margin-bottom: 1.5rem;
    }
    .quick-action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .quick-action-card.warning {
        border-left-color: #ffc107;
    }
    .quick-action-card.info {
        border-left-color: #17a2b8;
    }
    .quick-action-card.danger {
        border-left-color: #dc3545;
    }
    .quick-action-card.dark {
        border-left-color: #343a40;
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .avatar-lg {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 1.2rem;
    }
    
    .page-title {
        font-weight: 600;
        color: var(--primary-color);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-warehouse me-2"></i>Inventory Management
            </h1>
            <p class="text-muted">Manage your inventory movements and track stock levels.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white border-bottom">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-exchange-alt me-2 text-primary"></i>Inventory Movements
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('staff.inventory.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> New Movement
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Reference</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventoryMovements as $movement)
                                    <tr>
                                        <td>{{ $movement->created_at->format('M d, Y H:i') }}</td>
                                        <td>{{ $movement->product->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $movement->type === 'in' ? 'success' : 'danger' }}">
                                                {{ ucfirst($movement->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{ $movement->quantity }}</strong>
                                        </td>
                                        <td>
                                            {{ ucfirst($movement->reference_type) }}
                                            @if($movement->reference_id)
                                                #{{ $movement->reference_id }}
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($movement->notes, 30) }}</td>
                                        <td>
                                            @if($movement->created_at->diffInHours(now()) <= 24)
                                                <a href="{{ route('staff.inventory.edit', $movement) }}" 
                                                   class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('staff.inventory.destroy', $movement) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Are you sure you want to delete this movement?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted">No actions</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No inventory movements found</h5>
                                            <p class="text-muted">Create your first inventory movement to get started</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $inventoryMovements->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card quick-action-card warning">
                <div class="card-header bg-white border-bottom">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Low Stock
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div class="stat-number text-warning">{{ $lowStockProducts ?? 0 }}</div>
                    <p class="text-muted">Products below minimum level</p>
                    <a href="{{ route('staff.inventory.reorder') }}" class="btn btn-warning btn-sm w-100">
                        <i class="fas fa-redo me-1"></i> Reorder Now
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card quick-action-card info">
                <div class="card-header bg-white border-bottom">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-truck me-2 text-info"></i>Pending Deliveries
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div class="stat-number text-info">{{ $pendingDeliveries ?? 0 }}</div>
                    <p class="text-muted">Awaiting receipt</p>
                    <a href="{{ route('staff.inventory.follow-up') }}" class="btn btn-info btn-sm w-100">
                        <i class="fas fa-truck-loading me-1"></i> Follow Up
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card quick-action-card danger">
                <div class="card-header bg-white border-bottom">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-trash me-2 text-danger"></i>Damaged/Expired
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div class="stat-number text-danger">{{ $expiredProducts ?? 0 }}</div>
                    <p class="text-muted">Items to discard</p>
                    <a href="{{ route('staff.inventory.discard') }}" class="btn btn-danger btn-sm w-100">
                        <i class="fas fa-trash-alt me-1"></i> Manage Items
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card quick-action-card dark">
                <div class="card-header bg-white border-bottom">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-times-circle me-2 text-dark"></i>Out of Stock
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div class="stat-number text-dark">{{ $outOfStockCount ?? 0 }}</div>
                    <p class="text-muted">Products with zero quantity</p>
                    <a href="{{ route('staff.inventory.reorder') }}" class="btn btn-dark btn-sm w-100">
                        <i class="fas fa-box-open me-1"></i> View Items
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection