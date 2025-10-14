@extends('layouts.butcher')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-user me-2"></i>Staff Profile: {{ $staff->name }}
            </h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('staff.edit', $staff) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
            <a href="{{ route('staff.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <x-alert/>

    <!-- Staff Information Card -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>
                        Staff Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th width="40%">Name:</th>
                                    <td>{{ $staff->name }}</td>
                                </tr>
                                <tr>
                                    <th>Position:</th>
                                    <td>{{ $staff->position }}</td>
                                </tr>
                                <tr>
                                    <th>Department:</th>
                                    <td>{{ $staff->department ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th width="40%">Contact:</th>
                                    <td>{{ $staff->contact_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Date Hired:</th>
                                    <td>{{ $staff->date_hired ? $staff->date_hired->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge rounded-pill bg-{{ $staff->status == 'Active' ? 'success' : 'secondary' }}">
                                            {{ $staff->status }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Records -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line me-2"></i>
                        Performance History
                    </h3>
                    <div class="card-actions">
                        <a href="{{ route('staff-performance.create', ['staff_id' => $staff->id]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add Performance Record
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($staff->performances->isEmpty())
                        <div class="text-center py-4">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No performance records found</p>
                            <a href="{{ route('staff-performance.create', ['staff_id' => $staff->id]) }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-1"></i>Add First Performance Record
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-vcenter mb-0">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Attendance</th>
                                        <th>Task Completion</th>
                                        <th>Feedback Score</th>
                                        <th class="text-center">Overall Score</th>
                                        <th>Grade</th>
                                        <th>Remarks</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($staff->performances as $performance)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($performance->month)->format('M Y') }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px; width: 100px;">
                                                <div class="progress-bar bg-primary" style="width: {{ $performance->attendance_rate }}%">
                                                    {{ number_format($performance->attendance_rate, 0) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 20px; width: 100px;">
                                                <div class="progress-bar bg-info" style="width: {{ $performance->task_completion_rate }}%">
                                                    {{ number_format($performance->task_completion_rate, 0) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ number_format($performance->customer_feedback_score, 1) }}/5
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $score = $performance->overall_performance;
                                                $color = $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
                                            @endphp
                                            <span class="badge bg-{{ $color }} fs-5">
                                                {{ number_format($score, 1) }}%
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $performance->grade }}</span>
                                        </td>
                                        <td>{{ $performance->remarks ?? 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group float-end">
                                                <a href="{{ route('staff-performance.edit', $performance) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('staff-performance.destroy', $performance) }}" method="POST" 
                                                    onsubmit="return confirm('Delete this performance record?');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
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

                        <!-- Performance Summary -->
                        <div class="mt-4 text-center">
                            <h4>Average Performance: 
                                <span class="badge bg-success fs-3">
                                    {{ number_format($staff->average_performance, 1) }}%
                                </span>
                            </h4>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="page-body">
    <div class="container-xl">
        <x-alert/>
        
        <!-- Staff Information Card -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user me-2"></i>
                            Staff Information
                        </h3>
                        <div class="card-actions">
                            <a href="{{ route('staff.edit', $staff) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>
                                Edit
                            </a>
                            <a href="{{ route('staff.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>
                                Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">Name:</th>
                                        <td>{{ $staff->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Position:</th>
                                        <td>{{ $staff->position }}</td>
                                    </tr>
                                    <tr>
                                        <th>Department:</th>
                                        <td>{{ $staff->department ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">Contact:</th>
                                        <td>{{ $staff->contact_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date Hired:</th>
                                        <td>{{ $staff->date_hired ? $staff->date_hired->format('M d, Y') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <span class="badge bg-{{ $staff->status == 'Active' ? 'success' : 'secondary' }}">
                                                {{ $staff->status }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Records -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line me-2"></i>
                            Performance History
                        </h3>
                        <div class="card-actions">
                            <a href="{{ route('staff-performance.create', ['staff_id' => $staff->id]) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i>
                                Add Performance Record
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($staff->performances->isEmpty())
                            <div class="text-center py-4">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No performance records found</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Attendance</th>
                                            <th>Task Completion</th>
                                            <th>Feedback Score</th>
                                            <th>Overall Score</th>
                                            <th>Grade</th>
                                            <th>Remarks</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($staff->performances as $performance)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($performance->month)->format('M Y') }}</td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" style="width: {{ $performance->attendance_rate }}%">
                                                        {{ number_format($performance->attendance_rate, 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-info" style="width: {{ $performance->task_completion_rate }}%">
                                                        {{ number_format($performance->task_completion_rate, 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ number_format($performance->customer_feedback_score, 1) }}/5
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $score = $performance->overall_performance;
                                                    $color = $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
                                                @endphp
                                                <span class="badge bg-{{ $color }}">
                                                    {{ number_format($score, 1) }}%
                                                </span>
                                            </td>
                                            <td>{{ $performance->grade }}</td>
                                            <td>{{ $performance->remarks ?? 'N/A' }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('staff-performance.edit', $performance) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('staff-performance.destroy', $performance) }}" method="POST" 
                                                        onsubmit="return confirm('Delete this performance record?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
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

                            <!-- Performance Summary -->
                            <div class="mt-4">
                                <h4>Average Performance: 
                                    <span class="badge bg-success">
                                        {{ number_format($staff->average_performance, 1) }}%
                                    </span>
                                </h4>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
