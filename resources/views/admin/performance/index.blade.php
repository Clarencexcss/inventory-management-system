@extends('layouts.butcher')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-chart-line me-2"></i>Performance Records
            </h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('staff-performance.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Performance Record
            </a>
            <a href="{{ route('staff.report') }}" class="btn btn-success">
                <i class="fas fa-chart-bar me-2"></i>View Report
            </a>
        </div>
    </div>

    <x-alert/>

    @if($performances->isEmpty())
        <div class="col-12 text-center py-5">
            <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
            <h3>No performance records found</h3>
            <p class="text-muted">Add performance evaluations to track staff performance</p>
            <a href="{{ route('staff-performance.create') }}" class="btn btn-primary mt-2">
                <i class="fas fa-plus me-1"></i>Add First Performance Record
            </a>
        </div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-vcenter card-table mb-0">
                        <thead>
                            <tr>
                                <th>Staff Member</th>
                                <th>Month</th>
                                <th>Attendance</th>
                                <th>Task Completion</th>
                                <th>Feedback</th>
                                <th class="text-center">Overall Score</th>
                                <th>Grade</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($performances as $performance)
                            <tr>
                                <td>
                                    <a href="{{ route('staff.show', $performance->staff) }}" class="text-decoration-none">
                                        <strong>{{ $performance->staff->name }}</strong>
                                    </a>
                                    <div class="small text-muted">{{ $performance->staff->position }}</div>
                                </td>
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
                                    <span class="badge bg-secondary">
                                        {{ $performance->grade }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group float-end">
                                        <a href="{{ route('staff-performance.show', $performance) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('staff-performance.edit', $performance) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('staff-performance.destroy', $performance) }}" method="POST" 
                                            onsubmit="return confirm('Are you sure you want to delete this record?');" class="d-inline">
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

        <div class="mt-3">
            {{ $performances->links() }}
        </div>
    @endif
</div>
@endsection

@section('content')
<div class="page-body">
    <div class="container-xl">
        <x-alert/>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line me-2"></i>
                    Performance Records
                </h3>
                <div class="card-actions">
                    <a href="{{ route('staff-performance.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Add Performance Record
                    </a>
                    <a href="{{ route('staff.report') }}" class="btn btn-success">
                        <i class="fas fa-chart-bar me-1"></i>
                        View Report
                    </a>
                </div>
            </div>

            <div class="card-body">
                @if($performances->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                        <h3>No performance records found</h3>
                        <p class="text-muted">Add performance evaluations to track staff performance</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Staff Member</th>
                                    <th>Month</th>
                                    <th>Attendance</th>
                                    <th>Task Completion</th>
                                    <th>Feedback</th>
                                    <th>Overall Score</th>
                                    <th>Grade</th>
                                    <th class="w-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($performances as $performance)
                                <tr>
                                    <td>
                                        <a href="{{ route('staff.show', $performance->staff) }}">
                                            {{ $performance->staff->name }}
                                        </a>
                                        <div class="small text-muted">{{ $performance->staff->position }}</div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($performance->month)->format('M Y') }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px; width: 100px;">
                                            <div class="progress-bar" style="width: {{ $performance->attendance_rate }}%">
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
                                    <td>
                                        @php
                                            $score = $performance->overall_performance;
                                            $color = $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
                                        @endphp
                                        <span class="badge bg-{{ $color }}">
                                            {{ number_format($score, 1) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $performance->grade }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('staff-performance.show', $performance) }}" class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('staff-performance.edit', $performance) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('staff-performance.destroy', $performance) }}" method="POST" 
                                                onsubmit="return confirm('Are you sure you want to delete this record?');">
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

                    <div class="mt-3">
                        {{ $performances->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
