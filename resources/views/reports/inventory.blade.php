@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-print-none">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="page-title">Inventory Analytics</h2>
                        <div class="text-muted mt-1">Real-time inventory insights and stock management</div>
                    </div>
                    <div class="col-auto ms-auto d-print-none">
                        <button onclick="refreshData()" class="btn btn-primary">
                            <i class="fas fa-sync-alt"></i> Refresh Data
                        </button>
                        <button onclick="exportData()" class="btn btn-success">
                            <i class="fas fa-file-export"></i> Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4" id="summaryCards">
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
                            <span class="bg-info text-white avatar">
                                <i class="fas fa-layer-group"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium" id="totalStock">Loading...</div>
                            <div class="text-muted">Total Stock Count</div>
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
                            <span class="bg-danger text-white avatar">
                                <i class="fas fa-clock"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium" id="soonToExpire">Loading...</div>
                            <div class="text-muted">Soon to Expire</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="row mb-4" id="lowStockAlert" style="display: none;">
        <div class="col-12">
            <div class="alert alert-warning">
                <h5><i class="fas fa-exclamation-triangle"></i> Low Stock Alert</h5>
                <p>The following products are running low on stock (less than 10 units):</p>
                <div id="lowStockList"></div>
            </div>
        </div>
    </div>

    <!-- Expiration Alert -->
    <div class="row mb-4" id="expirationAlert" style="display: none;">
        <div class="col-12">
            <div class="alert alert-danger">
                <h5><i class="fas fa-clock"></i> Expiration Alert</h5>
                <p>The following products will expire within 7 days:</p>
                <div id="expirationList"></div>
            </div>
        </div>
    </div>

    <!-- Inventory Details -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Inventory Details</h3>
                    <div class="card-actions">
                        <span class="badge bg-success" id="lastUpdated">Last updated: Never</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="inventoryDetails">
                        <div class="col-12 text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading inventory data...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Products Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Low Stock Products</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="lowStockTable">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Current Stock</th>
                                    <th>Expiration Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Soon to Expire Products Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Soon to Expire Products</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="expirationTable">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Current Stock</th>
                                    <th>Expiration Date</th>
                                    <th>Days Until Expiry</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let inventoryData = {};

// Load inventory data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadInventoryData();
    
    // Auto-refresh every 2 minutes
    setInterval(loadInventoryData, 120000);
});

// Load inventory analytics data
async function loadInventoryData() {
    try {
        const response = await fetch('/api/analytics/inventory');
        const data = await response.json();
        
        if (data.status === 'success') {
            inventoryData = data.data;
            updateSummaryCards();
            updateAlerts();
            updateInventoryDetails();
            updateTables();
            updateLastUpdated();
        } else {
            console.error('Failed to load inventory data:', data.message);
            showError('Failed to load inventory data: ' + data.message);
        }
    } catch (error) {
        console.error('Error loading inventory data:', error);
        showError('Error loading inventory data: ' + error.message);
    }
}

// Update summary cards
function updateSummaryCards() {
    document.getElementById('totalItems').textContent = inventoryData.total_items || 0;
    document.getElementById('totalStock').textContent = inventoryData.total_stock_count || 0;
    document.getElementById('lowStockItems').textContent = inventoryData.low_stock_items || 0;
    document.getElementById('soonToExpire').textContent = inventoryData.soon_to_expire_items || 0;
}

// Update alerts
function updateAlerts() {
    // Low stock alert
    if (inventoryData.low_stock_items > 0) {
        const lowStockAlert = document.getElementById('lowStockAlert');
        const lowStockList = document.getElementById('lowStockList');
        
        let listHtml = '<ul>';
        inventoryData.low_stock_products.forEach(product => {
            listHtml += `<li><strong>${product.name}</strong> - Current Stock: ${product.quantity}</li>`;
        });
        listHtml += '</ul>';
        
        lowStockList.innerHTML = listHtml;
        lowStockAlert.style.display = 'block';
    } else {
        document.getElementById('lowStockAlert').style.display = 'none';
    }
    
    // Expiration alert
    if (inventoryData.soon_to_expire_items > 0) {
        const expirationAlert = document.getElementById('expirationAlert');
        const expirationList = document.getElementById('expirationList');
        
        let listHtml = '<ul>';
        inventoryData.soon_to_expire_products.forEach(product => {
            const daysUntilExpiry = Math.ceil((new Date(product.expiration_date) - new Date()) / (1000 * 60 * 60 * 24));
            listHtml += `<li><strong>${product.name}</strong> - Expires: ${product.expiration_date} (${daysUntilExpiry} days)</li>`;
        });
        listHtml += '</ul>';
        
        expirationList.innerHTML = listHtml;
        expirationAlert.style.display = 'block';
    } else {
        document.getElementById('expirationAlert').style.display = 'none';
    }
}

// Update inventory details
function updateInventoryDetails() {
    const container = document.getElementById('inventoryDetails');
    
    container.innerHTML = `
        <div class="col-md-6">
            <h5>Inventory Overview</h5>
            <p><strong>Total Products:</strong> ${inventoryData.total_items || 0}</p>
            <p><strong>Total Stock Count:</strong> ${inventoryData.total_stock_count || 0}</p>
            <p><strong>Low Stock Items:</strong> ${inventoryData.low_stock_items || 0}</p>
            <p><strong>Soon to Expire:</strong> ${inventoryData.soon_to_expire_items || 0}</p>
        </div>
        <div class="col-md-6">
            <h5>Stock Health</h5>
            <div class="progress mb-2">
                <div class="progress-bar bg-success" style="width: ${getStockHealthPercentage()}%"></div>
            </div>
            <small class="text-muted">Stock Health: ${getStockHealthPercentage()}%</small>
            <p class="mt-2"><strong>Status:</strong> ${getStockHealthStatus()}</p>
        </div>
    `;
}

// Update tables
function updateTables() {
    updateLowStockTable();
    updateExpirationTable();
}

// Update low stock table
function updateLowStockTable() {
    const tbody = document.querySelector('#lowStockTable tbody');
    
    if (inventoryData.low_stock_products && inventoryData.low_stock_products.length > 0) {
        let html = '';
        inventoryData.low_stock_products.forEach(product => {
            html += `
                <tr>
                    <td>${product.name}</td>
                    <td><span class="badge bg-warning">${product.quantity}</span></td>
                    <td>${product.expiration_date || 'N/A'}</td>
                    <td><span class="badge bg-danger">Low Stock</span></td>
                    <td>
                        <a href="/products" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Manage
                        </a>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    } else {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-success">No low stock items found!</td></tr>';
    }
}

// Update expiration table
function updateExpirationTable() {
    const tbody = document.querySelector('#expirationTable tbody');
    
    if (inventoryData.soon_to_expire_products && inventoryData.soon_to_expire_products.length > 0) {
        let html = '';
        inventoryData.soon_to_expire_products.forEach(product => {
            const daysUntilExpiry = Math.ceil((new Date(product.expiration_date) - new Date()) / (1000 * 60 * 60 * 24));
            const badgeClass = daysUntilExpiry <= 3 ? 'bg-danger' : 'bg-warning';
            
            html += `
                <tr>
                    <td>${product.name}</td>
                    <td>${product.quantity}</td>
                    <td>${product.expiration_date}</td>
                    <td><span class="badge ${badgeClass}">${daysUntilExpiry} days</span></td>
                    <td>
                        <a href="/products" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Manage
                        </a>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    } else {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-success">No products expiring soon!</td></tr>';
    }
}

// Calculate stock health percentage
function getStockHealthPercentage() {
    if (!inventoryData.total_items || inventoryData.total_items === 0) return 100;
    
    const lowStockPercentage = (inventoryData.low_stock_items / inventoryData.total_items) * 100;
    return Math.max(0, 100 - lowStockPercentage);
}

// Get stock health status
function getStockHealthStatus() {
    const percentage = getStockHealthPercentage();
    
    if (percentage >= 90) return 'Excellent';
    if (percentage >= 75) return 'Good';
    if (percentage >= 50) return 'Fair';
    return 'Needs Attention';
}

// Update last updated timestamp
function updateLastUpdated() {
    const now = new Date();
    document.getElementById('lastUpdated').textContent = `Last updated: ${now.toLocaleTimeString()}`;
}

// Refresh data
function refreshData() {
    loadInventoryData();
}

// Export data
function exportData() {
    if (Object.keys(inventoryData).length === 0) {
        alert('No data to export. Please refresh the data first.');
        return;
    }
    
    const dataStr = JSON.stringify(inventoryData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `inventory-analytics-${new Date().toISOString().split('T')[0]}.json`;
    link.click();
    URL.revokeObjectURL(url);
}

// Show error message
function showError(message) {
    const container = document.getElementById('inventoryDetails');
    container.innerHTML = `
        <div class="col-12">
            <div class="alert alert-danger">
                <h5><i class="fas fa-exclamation-circle"></i> Error</h5>
                <p>${message}</p>
                <button onclick="loadInventoryData()" class="btn btn-danger">
                    <i class="fas fa-retry"></i> Retry
                </button>
            </div>
        </div>
    `;
}
</script>
@endpush
