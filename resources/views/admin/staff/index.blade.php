@extends('layouts.butcher')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-users me-2"></i>Staff Management
            </h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('staff.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Staff
            </a>
        </div>
    </div>

    <x-alert/>

    @if($staff->isEmpty())
        <div class="col-12 text-center py-5">
            <i class="fas fa-users fa-4x text-muted mb-3"></i>
            <h3>No staff members found</h3>
            <p class="text-muted">Add your first staff member to get started</p>
            <a href="{{ route('staff.create') }}" class="btn btn-primary mt-2">
                <i class="fas fa-plus me-1"></i>Add your first Staff Member
            </a>
        </div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-vcenter card-table mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Department</th>
                                <th>Contact</th>
                                <th>Date Hired</th>
                                <th>Status</th>
                                <th class="text-center">Avg Performance</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staff as $member)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm me-2" style="background-color: var(--primary-color); color: white;">
                                            {{ strtoupper(substr($member->name, 0, 2)) }}
                                        </span>
                                        <strong>{{ $member->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $member->position }}</td>
                                <td>{{ $member->department ?? 'N/A' }}</td>
                                <td>{{ $member->contact_number ?? 'N/A' }}</td>
                                <td>{{ $member->date_hired ? $member->date_hired->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <span class="badge rounded-pill bg-{{ $member->status == 'Active' ? 'success' : 'secondary' }}">
                                        {{ $member->status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $avgPerf = $member->performances_avg_overall_performance ?? 0;
                                        $badgeColor = $avgPerf >= 80 ? 'success' : ($avgPerf >= 60 ? 'warning' : 'danger');
                                    @endphp
                                    <span class="badge bg-{{ $badgeColor }} fs-5">
                                        {{ number_format($avgPerf, 1) }}%
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group float-end">
                                        <a href="{{ route('staff.show', $member) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('staff.edit', $member) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('staff.destroy', $member) }}" method="POST" 
                                            onsubmit="return confirm('Are you sure you want to delete this staff member?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
