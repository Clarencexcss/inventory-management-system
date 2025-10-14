@extends('layouts.butcher')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-info-circle me-2"></i>Performance Details
            </h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('staff-performance.edit', $staffPerformance) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
            <a href="{{ route('staff-performance.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <x-alert/>
    <x-alert/>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user me-2"></i>Staff Information
            </h3>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="40%">Name:</th>
                            <td>{{ $staffPerformance->staff->name }}</td>
                        </tr>
                        <tr>
                            <th>Position:</th>
                            <td>{{ $staffPerformance->staff->position }}</td>
                        </tr>
                        <tr>
                            <th>Department:</th>
                            <td>{{ $staffPerformance->staff->department ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="40%">Month:</th>
                            <td>{{ \Carbon\Carbon::parse($staffPerformance->month)->format('F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Evaluated On:</th>
                            <td>{{ $staffPerformance->created_at->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $staffPerformance->updated_at->format('M d, Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-bar me-2"></i>Performance Metrics
            </h3>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="card-title">Attendance Rate</h5>
                            <div class="progress mb-2" style="height: 30px;">
                                <div class="progress-bar bg-primary" style="width: {{ $staffPerformance->attendance_rate }}%">
                                    {{ number_format($staffPerformance->attendance_rate, 1) }}%
                                </div>
                            </div>
                            <small class="text-muted">Weight: 30%</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="card-title">Task Completion Rate</h5>
                            <div class="progress mb-2" style="height: 30px;">
                                <div class="progress-bar bg-info" style="width: {{ $staffPerformance->task_completion_rate }}%">
                                    {{ number_format($staffPerformance->task_completion_rate, 1) }}%
                                </div>
                            </div>
                            <small class="text-muted">Weight: 40%</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="card-title">Customer Feedback</h5>
                            <div class="h1 mb-2">
                                <span class="badge bg-primary">
                                    {{ number_format($staffPerformance->customer_feedback_score, 1) }}/5.0
                                </span>
                            </div>
                            <small class="text-muted">Weight: 30%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body text-center">
            <h4>Overall Performance Score</h4>
            @php
                $score = $staffPerformance->overall_performance;
                $color = $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
            @endphp
            <div class="display-1 mb-3">
                <span class="badge bg-{{ $color }}">
                    {{ number_format($score, 1) }}%
                </span>
            </div>
            <h5>
                <span class="badge bg-{{ $color }}">
                    {{ $staffPerformance->grade }}
                </span>
            </h5>
            <p class="text-muted">
                Formula: (Attendance × 30%) + (Task Completion × 40%) + (Feedback × 30%)
            </p>
        </div>
    </div>

    @if($staffPerformance->remarks)
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-comment me-2"></i>Remarks
            </h3>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-0">
                {{ $staffPerformance->remarks }}
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-body text-center">
            <a href="{{ route('staff.show', $staffPerformance->staff) }}" class="btn btn-primary">
                <i class="fas fa-user me-1"></i>
                View Staff Profile
            </a>
            <a href="{{ route('staff-performance.edit', $staffPerformance) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i>
                Edit Evaluation
            </a>
            <form action="{{ route('staff-performance.destroy', $staffPerformance) }}" method="POST" 
                style="display: inline-block;" 
                onsubmit="return confirm('Are you sure you want to delete this performance record?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>
                    Delete Record
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h4>Staff Information</h4>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Name:</th>
                                <td>{{ $staffPerformance->staff->name }}</td>
                            </tr>
                            <tr>
                                <th>Position:</th>
                                <td>{{ $staffPerformance->staff->position }}</td>
                            </tr>
                            <tr>
                                <th>Department:</th>
                                <td>{{ $staffPerformance->staff->department ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h4>Evaluation Period</h4>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Month:</th>
                                <td>{{ \Carbon\Carbon::parse($staffPerformance->month)->format('F Y') }}</td>
                            </tr>
                            <tr>
                                <th>Evaluated On:</th>
                                <td>{{ $staffPerformance->created_at->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated:</th>
                                <td>{{ $staffPerformance->updated_at->format('M d, Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h4>Performance Metrics</h4>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5 class="card-title">Attendance Rate</h5>
                                <div class="progress mb-2" style="height: 30px;">
                                    <div class="progress-bar" style="width: {{ $staffPerformance->attendance_rate }}%">
                                        {{ number_format($staffPerformance->attendance_rate, 1) }}%
                                    </div>
                                </div>
                                <small class="text-muted">Weight: 30%</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5 class="card-title">Task Completion Rate</h5>
                                <div class="progress mb-2" style="height: 30px;">
                                    <div class="progress-bar bg-info" style="width: {{ $staffPerformance->task_completion_rate }}%">
                                        {{ number_format($staffPerformance->task_completion_rate, 1) }}%
                                    </div>
                                </div>
                                <small class="text-muted">Weight: 40%</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5 class="card-title">Customer Feedback</h5>
                                <div class="h1 mb-2">
                                    <span class="badge bg-primary">
                                        {{ number_format($staffPerformance->customer_feedback_score, 1) }}/5.0
                                    </span>
                                </div>
                                <small class="text-muted">Weight: 30%</small>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row mb-4">
                    <div class="col-md-12 text-center">
                        <h4>Overall Performance Score</h4>
                        @php
                            $score = $staffPerformance->overall_performance;
                            $color = $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
                        @endphp
                        <div class="display-1 mb-3">
                            <span class="badge bg-{{ $color }}">
                                {{ number_format($score, 1) }}%
                            </span>
                        </div>
                        <h5>
                            <span class="badge bg-{{ $color }}">
                                {{ $staffPerformance->grade }}
                            </span>
                        </h5>
                        <p class="text-muted">
                            Formula: (Attendance × 30%) + (Task Completion × 40%) + (Feedback × 30%)
                        </p>
                    </div>
                </div>

                @if($staffPerformance->remarks)
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Remarks</h4>
                            <div class="alert alert-info">
                                {{ $staffPerformance->remarks }}
                            </div>
                        </div>
                    </div>
                @endif

                <hr>

                <div class="row">
                    <div class="col-md-12 text-center">
                        <a href="{{ route('staff.show', $staffPerformance->staff) }}" class="btn btn-primary">
                            <i class="fas fa-user me-1"></i>
                            View Staff Profile
                        </a>
                        <a href="{{ route('staff-performance.edit', $staffPerformance) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>
                            Edit Evaluation
                        </a>
                        <form action="{{ route('staff-performance.destroy', $staffPerformance) }}" method="POST" 
                            style="display: inline-block;" 
                            onsubmit="return confirm('Are you sure you want to delete this performance record?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i>
                                Delete Record
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
