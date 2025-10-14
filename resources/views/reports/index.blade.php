@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-print-none">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="page-title">Analytics Dashboard</h2>
                        <div class="text-muted mt-1">Real-time insights for ButcherPro Management System</div>
                    </div>
                    <div class="col-auto ms-auto d-print-none">
                        <button onclick="refreshAllData()" class="btn btn-primary">
                            <i class="fas fa-sync-alt"></i> Refresh Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Overview -->
    <div class="row mb-4" id="quickStats">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <i class="fas fa-boxes"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium" id="totalItems">Loading...</div>
                            <div class="text-muted">Total Products</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-success text-white avatar">
                                <i class="fas fa-chart-line"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium" id="totalSales">Loading...</div>
                            <div class="text-muted">Total Sales</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-warning text-white avatar">
                                <i class="fas fa-exclamation-triangle"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium" id="lowStockItems">Loading...</div>
                            <div class="text-muted">Low Stock Items</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-info text-white avatar">
                                <i class="fas fa-users"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium" id="totalStaff">Loading...</div>
                            <div class="text-muted">Total Staff</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="row">
        <!-- Inventory Analytics -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <i class="fas fa-boxes"></i>
                            </span>
                        </div>
                        <div class="col">
                            <h5 class="card-title">Inventory Analytics</h5>
                            <p class="card-text">Real-time inventory insights, stock levels, and expiration tracking.</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('reports.inventory') }}" class="btn btn-primary">
                            <i class="fas fa-chart-bar"></i> View Report
                        </a>
                        <button onclick="loadInventoryData()" class="btn btn-outline-primary">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Analytics -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-success text-white avatar">
                                <i class="fas fa-chart-line"></i>
                            </span>
                        </div>
                        <div class="col">
                            <h5 class="card-title">Sales Analytics</h5>
                            <p class="card-text">Sales performance, trends, and top-selling products analysis.</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('reports.sales') }}" class="btn btn-success">
                            <i class="fas fa-chart-bar"></i> View Report
                        </a>
                        <button onclick="loadSalesData()" class="btn btn-outline-success">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supplier Analytics -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-warning text-white avatar">
                                <i class="fas fa-truck"></i>
                            </span>
                        </div>
                        <div class="col">
                            <h5 class="card-title">Supplier Analytics</h5>
                            <p class="card-text">Supplier performance, delivery tracking, and procurement insights.</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('reports.purchases') }}" class="btn btn-warning">
                            <i class="fas fa-chart-bar"></i> View Report
                        </a>
                        <button onclick="loadSupplierData()" class="btn btn-outline-warning">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff Performance -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-info text-white avatar">
                                <i class="fas fa-users"></i>
                            </span>
                        </div>
                        <div class="col">
                            <h5 class="card-title">Staff Performance</h5>
                            <p class="card-text">Staff productivity, performance evaluations, and team analytics.</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('staff.report') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar"></i> View Report
                        </a>
                        <a href="{{ route('staff.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-users"></i> Manage Staff
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Endpoints Info -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-secondary text-white avatar">
                                <i class="fas fa-code"></i>
                            </span>
                        </div>
                        <div class="col">
                            <h5 class="card-title">API Endpoints</h5>
                            <p class="card-text">Access analytics data via REST API for external integrations.</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button onclick="showApiEndpoints()" class="btn btn-secondary">
                            <i class="fas fa-info-circle"></i> View Endpoints
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-dark text-white avatar">
                                <i class="fas fa-download"></i>
                            </span>
                        </div>
                        <div class="col">
                            <h5 class="card-title">Export Data</h5>
                            <p class="card-text">Export analytics data in various formats for external use.</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button onclick="exportAllData()" class="btn btn-dark">
                            <i class="fas fa-file-export"></i> Export All
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Data Display -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Live Analytics Data</h3>
                    <div class="card-actions">
                        <span class="badge bg-success" id="lastUpdated">Last updated: Never</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="liveData">
                        <div class="col-12 text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading analytics data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- API Endpoints Modal -->
<div class="modal fade" id="apiEndpointsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Analytics API Endpoints</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Endpoint</th>
                                <th>Method</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>/api/analytics/inventory</code></td>
                                <td><span class="badge bg-primary">GET</span></td>
                                <td>Inventory analytics and stock levels</td>
                                <td><button onclick="testEndpoint('/api/analytics/inventory')" class="btn btn-sm btn-outline-primary">Test</button></td>
                            </tr>
                            <tr>
                                <td><code>/api/analytics/sales</code></td>
                                <td><span class="badge bg-primary">GET</span></td>
                                <td>Sales performance and trends</td>
                                <td><button onclick="testEndpoint('/api/analytics/sales')" class="btn btn-sm btn-outline-primary">Test</button></td>
                            </tr>
                            <tr>
                                <td><code>/api/analytics/suppliers</code></td>
                                <td><span class="badge bg-primary">GET</span></td>
                                <td>Supplier performance analytics</td>
                                <td><button onclick="testEndpoint('/api/analytics/suppliers')" class="btn btn-sm btn-outline-primary">Test</button></td>
                            </tr>
                            <tr>
                                <td><code>/api/analytics/staff</code></td>
                                <td><span class="badge bg-primary">GET</span></td>
                                <td>Staff performance metrics</td>
                                <td><button onclick="testEndpoint('/api/analytics/staff')" class="btn btn-sm btn-outline-primary">Test</button></td>
                            </tr>
                            <tr>
                                <td><code>/api/analytics/dashboard</code></td>
                                <td><span class="badge bg-primary">GET</span></td>
                                <td>Combined analytics data</td>
                                <td><button onclick="testEndpoint('/api/analytics/dashboard')" class="btn btn-sm btn-outline-primary">Test</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Global variables for API data
let analyticsData = {};

// Load all analytics data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadAllData();
    
    // Auto-refresh every 5 minutes
    setInterval(loadAllData, 300000);
});

// Load all analytics data
async function loadAllData() {
    try {
        const response = await fetch('/api/analytics/dashboard');
        const data = await response.json();
        
        if (data.status === 'success') {
            analyticsData = data.data;
            updateQuickStats();
            updateLiveData();
            updateLastUpdated();
        } else {
            console.error('Failed to load analytics data:', data.message);
        }
    } catch (error) {
        console.error('Error loading analytics data:', error);
    }
}

// Update quick stats
function updateQuickStats() {
    if (analyticsData.inventory) {
        document.getElementById('totalItems').textContent = analyticsData.inventory.total_items || 0;
        document.getElementById('lowStockItems').textContent = analyticsData.inventory.low_stock_items || 0;
    }
    
    if (analyticsData.sales) {
        const totalSales = analyticsData.sales.total_sales || 0;
        document.getElementById('totalSales').textContent = '₱' + totalSales.toLocaleString();
    }
    
    if (analyticsData.staff) {
        document.getElementById('totalStaff').textContent = analyticsData.staff.total_staff || 0;
    }
}

// Update live data display
function updateLiveData() {
    const liveDataContainer = document.getElementById('liveData');
    
    if (!analyticsData.inventory || !analyticsData.sales) {
        liveDataContainer.innerHTML = '<div class="col-12 text-center"><p class="text-muted">No data available</p></div>';
        return;
    }
    
    liveDataContainer.innerHTML = `
        <div class="col-md-6">
            <h5>Top Selling Product</h5>
            <p class="h3 text-success">${analyticsData.sales.top_product || 'No data'}</p>
            <small class="text-muted">Quantity sold: ${analyticsData.sales.top_product_quantity || 0}</small>
        </div>
        <div class="col-md-6">
            <h5>Most Active Supplier</h5>
            <p class="h3 text-warning">${analyticsData.suppliers?.most_frequent_supplier || 'No data'}</p>
            <small class="text-muted">Deliveries: ${analyticsData.suppliers?.most_frequent_supplier_count || 0}</small>
        </div>
        <div class="col-md-6 mt-3">
            <h5>Average Daily Sales</h5>
            <p class="h3 text-primary">₱${(analyticsData.sales.average_daily_sales || 0).toLocaleString()}</p>
        </div>
        <div class="col-md-6 mt-3">
            <h5>Soon to Expire Items</h5>
            <p class="h3 text-danger">${analyticsData.inventory.soon_to_expire_items || 0}</p>
        </div>
    `;
}

// Update last updated timestamp
function updateLastUpdated() {
    const now = new Date();
    document.getElementById('lastUpdated').textContent = `Last updated: ${now.toLocaleTimeString()}`;
}

// Individual data loading functions
async function loadInventoryData() {
    try {
        const response = await fetch('/api/analytics/inventory');
        const data = await response.json();
        if (data.status === 'success') {
            analyticsData.inventory = data.data;
            updateQuickStats();
            updateLiveData();
        }
    } catch (error) {
        console.error('Error loading inventory data:', error);
    }
}

async function loadSalesData() {
    try {
        const response = await fetch('/api/analytics/sales');
        const data = await response.json();
        if (data.status === 'success') {
            analyticsData.sales = data.data;
            updateQuickStats();
            updateLiveData();
        }
    } catch (error) {
        console.error('Error loading sales data:', error);
    }
}

async function loadSupplierData() {
    try {
        const response = await fetch('/api/analytics/suppliers');
        const data = await response.json();
        if (data.status === 'success') {
            analyticsData.suppliers = data.data;
            updateLiveData();
        }
    } catch (error) {
        console.error('Error loading supplier data:', error);
    }
}

async function loadStaffData() {
    try {
        const response = await fetch('/api/analytics/staff');
        const data = await response.json();
        if (data.status === 'success') {
            analyticsData.staff = data.data;
            updateQuickStats();
        }
    } catch (error) {
        console.error('Error loading staff data:', error);
    }
}

// Refresh all data
function refreshAllData() {
    loadAllData();
}

// Show API endpoints modal
function showApiEndpoints() {
    const modal = new bootstrap.Modal(document.getElementById('apiEndpointsModal'));
    modal.show();
}

// Test API endpoint
async function testEndpoint(endpoint) {
    try {
        const response = await fetch(endpoint);
        const data = await response.json();
        
        if (data.status === 'success') {
            alert('API endpoint working! Check console for data.');
            console.log(`${endpoint} response:`, data);
        } else {
            alert('API endpoint returned error: ' + data.message);
        }
    } catch (error) {
        alert('Error testing endpoint: ' + error.message);
    }
}

// Export all data
function exportAllData() {
    if (Object.keys(analyticsData).length === 0) {
        alert('No data to export. Please refresh the data first.');
        return;
    }
    
    const dataStr = JSON.stringify(analyticsData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `butcherpro-analytics-${new Date().toISOString().split('T')[0]}.json`;
    link.click();
    URL.revokeObjectURL(url);
}
</script>
@endpush
