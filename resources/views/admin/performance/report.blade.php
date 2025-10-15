@extends('layouts.butcher')

@push('page-styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .stat-card {
        border-left: 4px solid var(--primary-color);
    }
    .top-performer-card {
        border-left: 4px solid #28a745;
    }
    .needs-improvement-card {
        border-left: 4px solid #ffc107;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-chart-bar me-2"></i>Staff Performance Report
            </h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('staff.index') }}" class="btn btn-secondary">
                <i class="fas fa-users me-1"></i>
                View Staff
            </a>
            <a href="{{ route('staff-performance.index') }}" class="btn btn-primary">
                <i class="fas fa-list me-1"></i>
                All Records
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <i class="fas fa-users"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="h2 mb-0">{{ $staffAverages->count() }}</div>
                            <div class="text-muted">Total Staff</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-success text-white avatar">
                                <i class="fas fa-chart-line"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="h2 mb-0 text-success">
                                {{ number_format($staffAverages->avg('avg_performance'), 1) }}%
                            </div>
                            <div class="text-muted">Average Performance</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-info text-white avatar">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="h2 mb-0">{{ $processedMonthlyTrends->count() }}</div>
                            <div class="text-muted">Months Evaluated</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top and Bottom Performers -->
    <div class="row mb-4">
        <!-- Top Performers -->
        <div class="col-md-6">
            <div class="card top-performer-card">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-trophy me-2"></i>
                        Top 3 Performers
                    </h3>
                </div>
                <div class="card-body">
                    @if($topPerformers->isEmpty())
                        <p class="text-muted text-center">No performance data available</p>
                    @else
                        @foreach($topPerformers as $index => $performer)
                            <div class="d-flex align-items-center mb-3 {{ $loop->last ? '' : 'pb-3 border-bottom' }}">
                                <span class="badge bg-success fs-4 me-3">
                                    #{{ $index + 1 }}
                                </span>
                                <div class="flex-grow-1">
                                    <strong class="d-block">{{ $performer->staff->name }}</strong>
                                    <small class="text-muted">{{ $performer->staff->position }}</small>
                                </div>
                                <span class="badge bg-success fs-4">
                                    {{ number_format($performer->avg_performance, 1) }}%
                                </span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Bottom Performers -->
        <div class="col-md-6">
            <div class="card needs-improvement-card">
                <div class="card-header bg-warning">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Needs Improvement
                    </h3>
                </div>
                <div class="card-body">
                    @if($bottomPerformers->isEmpty())
                        <p class="text-muted text-center">No performance data available</p>
                    @else
                        @foreach($bottomPerformers as $index => $performer)
                            <div class="d-flex align-items-center mb-3 {{ $loop->last ? '' : 'pb-3 border-bottom' }}">
                                <div class="flex-grow-1">
                                    <strong class="d-block">{{ $performer->staff->name }}</strong>
                                    <small class="text-muted">{{ $performer->staff->position }}</small>
                                </div>
                                @php
                                    $score = $performer->avg_performance;
                                    $color = $score >= 60 ? 'warning' : 'danger';
                                @endphp
                                <span class="badge bg-{{ $color }} fs-4">
                                    {{ number_format($score, 1) }}%
                                </span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Staff Performance Bar Chart -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar me-2"></i>
                        Average Performance by Staff
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="staffPerformanceChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Monthly Trend Line Chart -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line me-2"></i>
                        Monthly Performance Trends
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Performance Metrics -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-table me-2"></i>
                        Detailed Performance Metrics
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-vcenter card-table mb-0">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Staff Name</th>
                                    <th>Position</th>
                                    <th>Average Performance</th>
                                    <th>Grade</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staffAverages as $index => $staffAvg)
                                    @php
                                        $score = $staffAvg->avg_performance;
                                        $color = $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
                                        $grade = $score >= 90 ? 'Excellent' : ($score >= 80 ? 'Very Good' : ($score >= 70 ? 'Good' : ($score >= 60 ? 'Satisfactory' : 'Needs Improvement')));
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $index + 1 }}</strong></td>
                                        <td>
                                            <a href="{{ route('staff.show', $staffAvg->staff) }}" class="text-decoration-none">
                                                <strong>{{ $staffAvg->staff->name }}</strong>
                                            </a>
                                        </td>
                                        <td>{{ $staffAvg->staff->position }}</td>
                                        <td>
                                            <div class="progress" style="height: 25px; min-width: 150px;">
                                                <div class="progress-bar bg-{{ $color }}" style="width: {{ $score }}%">
                                                    {{ number_format($score, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $color }}">
                                                {{ $grade }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('staff.show', $staffAvg->staff) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i>
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
    // Staff Performance Bar Chart
    const staffCtx = document.getElementById('staffPerformanceChart').getContext('2d');
    const staffPerformanceChart = new Chart(staffCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($staffAverages->pluck('staff.name')) !!},
            datasets: [{
                label: 'Average Performance (%)',
                data: {!! json_encode($staffAverages->pluck('avg_performance')) !!},
                backgroundColor: function(context) {
                    const value = context.parsed.y;
                    return value >= 80 ? 'rgba(40, 167, 69, 0.8)' : 
                           value >= 60 ? 'rgba(255, 193, 7, 0.8)' : 
                           'rgba(220, 53, 69, 0.8)';
                },
                borderColor: function(context) {
                    const value = context.parsed.y;
                    return value >= 80 ? 'rgb(40, 167, 69)' : 
                           value >= 60 ? 'rgb(255, 193, 7)' : 
                           'rgb(220, 53, 69)';
                },
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Performance: ' + context.parsed.y.toFixed(1) + '%';
                        }
                    }
                }
            }
        }
    });

    // Monthly Trend Line Chart
    const monthlyCtx = document.getElementById('monthlyTrendChart').getContext('2d');
    const monthlyTrendChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($processedMonthlyTrends->pluck('formatted_month')) !!},
            datasets: [
                {
                    label: 'Overall Performance',
                    data: {!! json_encode($processedMonthlyTrends->pluck('avg_performance')) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    fill: true
                },
                {
                    label: 'Attendance',
                    data: {!! json_encode($processedMonthlyTrends->pluck('avg_attendance')) !!},
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.1,
                    fill: true
                },
                {
                    label: 'Task Completion',
                    data: {!! json_encode($processedMonthlyTrends->pluck('avg_task_completion')) !!},
                    borderColor: 'rgb(255, 159, 64)',
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    tension: 0.1,
                    fill: true
                },
                {
                    label: 'Customer Feedback',
                    data: {!! json_encode($processedMonthlyTrends->pluck('avg_feedback')->map(function($score) {
                        return round(($score / 5) * 100, 2); // Convert to percentage
                    })) !!},
                    borderColor: 'rgb(153, 102, 255)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    tension: 0.1,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toFixed(1) + '%';
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
