@extends('layouts.butcher')

@push('page-styles')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    }
    .chart-container {
        position: relative;
        height: 400px;
    }
    .profit-positive { color: #28a745; font-weight: bold; }
    .profit-negative { color: #dc3545; font-weight: bold; }
    .month-input-row input {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 0.5rem;
    }
    .month-input-row input:focus {
        border-color: #8B0000;
        box-shadow: 0 0 0 0.2rem rgba(139, 0, 0, 0.25);
    }
    .btn-save-month {
        background-color: #8B0000;
        border-color: #8B0000;
        color: white;
    }
    .btn-save-month:hover {
        background-color: #6d0000;
        border-color: #6d0000;
    }
    .action-buttons .btn {
        margin: 0 5px;
    }
    @media print {
        .no-print { display: none !important; }
        .card { page-break-inside: avoid; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-header d-print-none no-print">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="page-title">
                            <i class="fas fa-chart-line text-success"></i> Sales Analytics
                        </h2>
                        <div class="text-muted mt-1">Comprehensive sales performance and trends analysis (2020-2025)</div>
                    </div>
                    <div class="col-auto ms-auto">
                        <div class="action-buttons">
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Reports
                            </a>
                            <button onclick="window.print()" class="btn btn-info">
                                <i class="fas fa-print"></i> Print
                            </button>
                            <a href="{{ route('reports.sales.analytics.export-csv') }}" class="btn btn-success">
                                <i class="fas fa-file-csv"></i> Export CSV
                            </a>
                            <a href="{{ route('reports.sales.analytics.export-pdf') }}" class="btn btn-danger">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-success text-white avatar">
                                <i class="fas fa-trophy"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $insights['highest_sales_year']->year ?? 'N/A' }}</div>
                            <div class="text-muted">Highest Sales Year</div>
                            <small class="text-success">₱{{ number_format($insights['highest_sales_year']->total_sales ?? 0, 2) }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <i class="fas fa-coins"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $insights['most_profitable_year']->year ?? 'N/A' }}</div>
                            <div class="text-muted">Most Profitable Year</div>
                            <small class="text-primary">₱{{ number_format($insights['most_profitable_year']->net_profit ?? 0, 2) }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-warning text-white avatar">
                                <i class="fas fa-star"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $insights['top_product']->product_name ?? 'N/A' }}</div>
                            <div class="text-muted">Top-Selling Product</div>
                            <small class="text-warning">
                                Qty: {{ number_format($insights['top_product']->total_quantity ?? 0) }} | 
                                Revenue: ₱{{ number_format($insights['top_product']->total_revenue ?? 0, 2) }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-4">
        <!-- Sales Performance Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line text-primary"></i> Sales Performance (2020-2025)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="salesPerformanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trends Analysis Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar text-success"></i> Annual Trends (2020-2025)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="trendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top-Selling Products Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie text-warning"></i> Top-Selling Products by Revenue
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2025 Manual Input Section -->
    <div class="row mb-4 no-print">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0 text-white">
                        <i class="fas fa-edit"></i> Manual Input for 2025 (January - December)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="15%">Month</th>
                                    <th width="25%">Total Sales (₱)</th>
                                    <th width="25%">Total Expenses (₱)</th>
                                    <th width="20%">Net Profit (₱)</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="months2025Table">
                                @foreach(range(1, 12) as $month)
                                    @php
                                        $monthName = date('F', mktime(0, 0, 0, $month, 1));
                                    @endphp
                                    <tr class="month-input-row" data-month="{{ $month }}">
                                        <td><strong>{{ $monthName }}</strong></td>
                                        <td>
                                            <input type="number" class="form-control sales-input" 
                                                   step="0.01" min="0" placeholder="Enter sales">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control expenses-input" 
                                                   step="0.01" min="0" placeholder="Enter expenses">
                                        </td>
                                        <td class="net-profit-display">-</td>
                                        <td>
                                            <button class="btn btn-sm btn-save-month" onclick="saveMonth({{ $month }})">
                                                <i class="fas fa-save"></i> Save
                                            </button>
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

    <!-- Top Products Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-trophy text-warning"></i> Top-Selling Products Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Qty Sold</th>
                                    <th>Revenue</th>
                                    <th>Profit</th>
                                    <th>Margin %</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $index => $product)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $product->product_name }}</strong></td>
                                        <td>{{ $product->category_name }}</td>
                                        <td>{{ number_format($product->total_quantity) }}</td>
                                        <td class="text-success">₱{{ number_format($product->total_revenue, 2) }}</td>
                                        <td class="{{ $product->total_profit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                                            ₱{{ number_format($product->total_profit, 2) }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $product->profit_margin >= 30 ? 'bg-success' : ($product->profit_margin >= 15 ? 'bg-warning' : 'bg-danger') }}">
                                                {{ number_format($product->profit_margin, 2) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No sales data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Yearly Summary Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table text-info"></i> Yearly Summary (2020-2024)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Year</th>
                                    <th>Total Sales</th>
                                    <th>Total Expenses</th>
                                    <th>Net Profit</th>
                                    <th>Profit Margin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($yearlySummary as $year)
                                    @php
                                        $margin = $year->total_sales > 0 ? ($year->net_profit / $year->total_sales) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $year->year }}</strong></td>
                                        <td>₱{{ number_format($year->total_sales, 2) }}</td>
                                        <td class="text-danger">₱{{ number_format($year->total_expenses, 2) }}</td>
                                        <td class="{{ $year->net_profit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                                            ₱{{ number_format($year->net_profit, 2) }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $margin >= 20 ? 'bg-success' : ($margin >= 10 ? 'bg-warning' : 'bg-danger') }}">
                                                {{ number_format($margin, 2) }}%
                                            </span>
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
// Chart colors
const chartColors = {
    primary: '#0d6efd',
    success: '#28a745',
    danger: '#dc3545',
    warning: '#ffc107',
    info: '#17a2b8',
    darkRed: '#8B0000'
};

// Prepare data for charts
const yearlySummary = @json($yearlySummary);
const topProducts = @json($topProducts);

// Sales Performance Chart (Line Chart)
const salesCtx = document.getElementById('salesPerformanceChart').getContext('2d');
new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: yearlySummary.map(y => y.year),
        datasets: [
            {
                label: 'Total Sales',
                data: yearlySummary.map(y => y.total_sales),
                borderColor: chartColors.success,
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            },
            {
                label: 'Total Expenses',
                data: yearlySummary.map(y => y.total_expenses),
                borderColor: chartColors.danger,
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.dataset.label + ': ₱' + ctx.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2})
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => '₱' + value.toLocaleString('en-PH')
                }
            }
        }
    }
});

// Trends Analysis Chart (Bar Chart)
const trendsCtx = document.getElementById('trendsChart').getContext('2d');
new Chart(trendsCtx, {
    type: 'bar',
    data: {
        labels: yearlySummary.map(y => y.year),
        datasets: [{
            label: 'Net Profit',
            data: yearlySummary.map(y => y.net_profit),
            backgroundColor: chartColors.primary,
            borderColor: chartColors.primary,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => 'Net Profit: ₱' + ctx.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2})
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => '₱' + value.toLocaleString('en-PH')
                }
            }
        }
    }
});

// Top Products Chart (Pie Chart)
const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
new Chart(topProductsCtx, {
    type: 'pie',
    data: {
        labels: topProducts.map(p => p.product_name),
        datasets: [{
            data: topProducts.map(p => p.total_revenue),
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'right' },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.label + ': ₱' + ctx.parsed.toLocaleString('en-PH', {minimumFractionDigits: 2})
                }
            }
        }
    }
});

// Load 2025 data via AJAX
function load2025Data() {
    fetch('{{ route("reports.sales.analytics.get-2025") }}')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                result.data.forEach(record => {
                    const row = document.querySelector(`tr[data-month="${record.month}"]`);
                    if (row) {
                        row.querySelector('.sales-input').value = record.total_sales;
                        row.querySelector('.expenses-input').value = record.total_expenses;
                        row.querySelector('.net-profit-display').innerHTML = 
                            `<span class="${record.net_profit >= 0 ? 'profit-positive' : 'profit-negative'}">₱${parseFloat(record.net_profit).toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>`;
                    }
                });
            }
        })
        .catch(error => console.error('Error loading 2025 data:', error));
}

// Auto-calculate net profit when inputs change
document.querySelectorAll('.sales-input, .expenses-input').forEach(input => {
    input.addEventListener('input', function() {
        const row = this.closest('tr');
        const sales = parseFloat(row.querySelector('.sales-input').value) || 0;
        const expenses = parseFloat(row.querySelector('.expenses-input').value) || 0;
        const netProfit = sales - expenses;
        
        row.querySelector('.net-profit-display').innerHTML = 
            `<span class="${netProfit >= 0 ? 'profit-positive' : 'profit-negative'}">₱${netProfit.toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>`;
    });
});

// Save month function
function saveMonth(month) {
    const row = document.querySelector(`tr[data-month="${month}"]`);
    const sales = parseFloat(row.querySelector('.sales-input').value);
    const expenses = parseFloat(row.querySelector('.expenses-input').value);

    if (!sales || !expenses) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Data',
            text: 'Please enter both sales and expenses!',
            confirmButtonColor: '#8B0000'
        });
        return;
    }

    // Show loading
    Swal.fire({
        title: 'Saving...',
        text: 'Please wait',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    // AJAX request
    fetch('{{ route("reports.sales.analytics.store-2025") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            month: month,
            total_sales: sales,
            total_expenses: expenses
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: result.message,
                confirmButtonColor: '#28a745',
                timer: 2000
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to save record',
                confirmButtonColor: '#dc3545'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while saving',
            confirmButtonColor: '#dc3545'
        });
    });
}

// Load 2025 data on page load
document.addEventListener('DOMContentLoaded', () => {
    load2025Data();
});
</script>
@endpush
