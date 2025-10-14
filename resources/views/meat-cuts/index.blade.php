@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-drumstick-bite me-2"></i>Meat Cuts Management
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Manage Meat Cuts</h3>
                    <a href="{{ route('meat-cuts.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add New Cut
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Search and Filter Section --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            <form method="GET" action="{{ route('meat-cuts.index') }}">
                                <div class="row g-3">
                                    {{-- Search Bar --}}
                                    <div class="col-md-4">
                                        <label class="form-label">Search</label>
                                        <input type="text" 
                                               name="search" 
                                               class="form-control" 
                                               placeholder="Search by name, animal type, or cut type..." 
                                               value="{{ request('search') }}">
                                    </div>

                                    {{-- Animal Type Filter --}}
                                    <div class="col-md-2">
                                        <label class="form-label">Animal Type</label>
                                        <select name="animal_type" class="form-select">
                                            <option value="">All Animals</option>
                                            @foreach($animalTypes as $type)
                                                <option value="{{ $type }}" {{ request('animal_type') == $type ? 'selected' : '' }}>
                                                    {{ ucfirst($type) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Cut Type Filter --}}
                                    <div class="col-md-2">
                                        <label class="form-label">Cut Type</label>
                                        <select name="cut_type" class="form-select">
                                            <option value="">All Cuts</option>
                                            @foreach($cutTypes as $type)
                                                <option value="{{ $type }}" {{ request('cut_type') == $type ? 'selected' : '' }}>
                                                    {{ ucfirst($type) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Availability Filter --}}
                                    <div class="col-md-2">
                                        <label class="form-label">Availability</label>
                                        <select name="availability" class="form-select">
                                            <option value="">All Status</option>
                                            <option value="1" {{ request('availability') == '1' ? 'selected' : '' }}>Available</option>
                                            <option value="0" {{ request('availability') == '0' ? 'selected' : '' }}>Not Available</option>
                                        </select>
                                    </div>

                                    {{-- Filter Buttons --}}
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="fas fa-filter me-1"></i>Filter
                                        </button>
                                        <a href="{{ route('meat-cuts.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-redo me-1"></i>Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Animal Type</th>
                                    <th>Cut Type</th>
                                    <th>Price/kg</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Min. Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($meatCuts as $cut)
                                    <tr>
                                        <td>
                                            @if($cut->image_path)
                                                <img src="{{ Storage::url($cut->image_path) }}" 
                                                     alt="{{ $cut->name }}" 
                                                     class="img-thumbnail" 
                                                     style="max-width: 100px;">
                                            @else
                                                <span class="text-muted">No image</span>
                                            @endif
                                        </td>
                                        <td>{{ $cut->name }}</td>
                                        <td>{{ ucfirst($cut->animal_type) }}</td>
                                        <td>{{ ucfirst($cut->cut_type) }}</td>
                                        <td>₱{{ number_format($cut->default_price_per_kg, 2) }}</td>
                                        <td>{{ $cut->quantity ?? 0 }}</td>
                                        <td>
                                            <span class="badge {{ $cut->is_available ? 'bg-success' : 'bg-danger' }}">
                                                {{ $cut->is_available ? 'Available' : 'Not Available' }}
                                            </span>
                                        </td>
                                        <td>{{ $cut->minimum_stock_level }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('meat-cuts.edit', $cut) }}" 
                                                   class="btn btn-sm btn-info me-2">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('meat-cuts.destroy', $cut) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Are you sure you want to delete this cut?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No meat cuts found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $meatCuts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-styles')
<style>
    .card-header {
        border-bottom: none;
    }
    .bg-danger {
        background-color: var(--primary-color) !important;
    }
</style>
@endpush 