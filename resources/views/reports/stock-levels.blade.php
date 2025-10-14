@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-print-none">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="page-title">Staff Performance Analytics</h2>
                        <div class="text-muted mt-1">Staff productivity, sales performance, and team analytics</div>
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
                            <div class="font-weight-medium" id="staffWithSales">Loading...</div>
                            <div class="text-muted">Staff with Sales</div>
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
                                <i class="fas fa-trophy"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium" id="topStaffSalesCount">Loading...</div>
                            <div class="text-muted">Top Staff Sales</div>
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
                                <i class="fas fa-dollar-sign"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium" id="topStaffSalesAmount">Loading...</div>
                            <div class="text-muted">Top Staff Revenue</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performer Highlight -->
    <div class="row mb-4" id="topPerformerCard" style="display: none;">
        <div class="col-12">
            <div class="card bg-gradient-info text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-white text-info avatar-lg">
                                <i class="fas fa-star"></i>
                            </span>
                        </div>
                        <div class="col">
                            <h3 class="card-title text-white">Top Performer</h3>
                            <h2 class="mb-0" id="topPerformerName">Loading...</h2>
                            <p class="mb-0" id="topPerformerDetails">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Performance Metrics -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Team Performance</h3>
                    <div class="card-actions">
                        <span class="badge bg-success" id="lastUpdated">Last updated: Never</span>
                    </div>
                </div>
                <div class="card-body" id="teamPerformance">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading team performance data...</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Performance Insights</h3>
                </div>
                <div class="card-body" id="performanceInsights">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading performance insights...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Performance Chart -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Staff Performance Overview</h3>
                </div>
                <div class="card-body">
                    <canvas id="staffPerformanceChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Performance Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Staff Performance Details</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="staffPerformanceTable">
                            <thead>
                                <tr>
                                    <th>Staff Name</th>
                                    <th>Role</th>
                                    <th>Sales Count</th>
                                    <th>Total Sales</th>
                                    <th>Average Sale</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">Loading...</td>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let staffData = {};
let staffChart = null;

// Load staff data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadStaffData();
    
    // Auto-refresh every 5 minutes
    setInterval(loadStaffData, 300000);
});

// Load staff analytics data
async function loadStaffData() {
    try {
        const response = await fetch('/api/analytics/staff');
        const data = await response.json();
        
        if (data.status === 'success') {
            staffData = data.data;
            updateSummaryCards();
            updateTopPerformer();
            updateTeamPerformance();
            updatePerformanceInsights();
            updateStaffPerformanceTable();
            updateStaffChart();
            updateLastUpdated();
        } else {
            console.error('Failed to load staff data:', data.message);
            showError('Failed to load staff data: ' + data.message);
        }
    } catch (error) {
        console.error('Error loading staff data:', error);
        showError('Error loading staff data: ' + error.message);
    }
}

// Update summary cards
function updateSummaryCards() {
    document.getElementById('totalStaff').textContent = staffData.total_staff || 0;
    document.getElementById('staffWithSales').textContent = staffData.staff_with_sales || 0;
    document.getElementById('topStaffSalesCount').textContent = staffData.top_staff_sales_count || 0;
    document.getElementById('topStaffSalesAmount').textContent = '₱' + (staffData.top_staff_sales_amount || 0).toLocaleString();
}

// Update top performer
function updateTopPerformer() {
    if (staffData.top_staff_by_amount && staffData.top_staff_by_amount !== 'No data') {
        document.getElementById('topPerformerName').textContent = staffData.top_staff_by_amount;
        document.getElementById('topPerformerDetails').textContent = `Sales: ${staffData.top_staff_sales_count || 0} | Revenue: ₱${(staffData.top_staff_sales_amount || 0).toLocaleString()}`;
        document.getElementById('topPerformerCard').style.display = 'block';
    } else {
        document.getElementById('topPerformerCard').style.display = 'none';
    }
}

// Update team performance
function updateTeamPerformance() {
    const container = document.getElementById('teamPerformance');
    
    const totalStaff = staffData.total_staff || 0;
    const staffWithSales = staffData.staff_with_sales || 0;
    const topStaffSalesCount = staffData.top_staff_sales_count || 0;
    const topStaffSalesAmount = staffData.top_staff_sales_amount || 0;
    
    const participationRate = totalStaff > 0 ? (staffWithSales / totalStaff) * 100 : 0;
    
    container.innerHTML = `
        <div class="row">
            <div class="col-12">
                <h5>Team Metrics</h5>
                <p><strong>Total Staff:</strong> ${totalStaff}</p>
                <p><strong>Active in Sales:</strong> ${staffWithSales}</p>
                <p><strong>Top Performer Sales:</strong> ${topStaffSalesCount}</p>
                <p><strong>Top Performer Revenue:</strong> ₱${topStaffSalesAmount.toLocaleString()}</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h5>Participation Rate</h5>
                <div class="progress mb-2">
                    <div class="progress-bar bg-success" style="width: ${participationRate}%"></div>
                </div>
                <small class="text-muted">${participationRate.toFixed(1)}% of staff are active in sales</small>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h5>Top Performers</h5>
                <p><strong>By Sales Count:</strong> ${staffData.top_staff_by_count || 'N/A'}</p>
                <p><strong>By Revenue:</strong> ${staffData.top_staff_by_amount || 'N/A'}</p>
            </div>
        </div>
    `;
}

// Update performance insights
function updatePerformanceInsights() {
    const container = document.getElementById('performanceInsights');
    
    const totalStaff = staffData.total_staff || 0;
    const staffWithSales = staffData.staff_with_sales || 0;
    const topStaffSalesAmount = staffData.top_staff_sales_amount || 0;
    const topStaffSalesCount = staffData.top_staff_sales_count || 0;
    
    const participationRate = totalStaff > 0 ? (staffWithSales / totalStaff) * 100 : 0;
    const averageSalesPerStaff = staffWithSales > 0 ? topStaffSalesAmount / staffWithSales : 0;
    
    container.innerHTML = `
        <div class="row">
            <div class="col-12">
                <h5>Performance Analysis</h5>
                <p><strong>Participation Rate:</strong> ${participationRate.toFixed(1)}%</p>
                <p><strong>Average Revenue per Staff:</strong> ₱${averageSalesPerStaff.toLocaleString()}</p>
                <p><strong>Performance Level:</strong> ${getPerformanceLevel(participationRate)}</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h5>Recommendations</h5>
                <ul class="list-unstyled">
                    ${getRecommendations(participationRate, staffWithSales)}
                </ul>
            </div>
        </div>
    `;
}

// Update staff performance table
function updateStaffPerformanceTable() {
    const tbody = document.querySelector('#staffPerformanceTable tbody');
    
    if (staffData.staff_performance && staffData.staff_performance.length > 0) {
        let html = '';
        staffData.staff_performance.forEach(staff => {
            const performance = getStaffPerformanceLevel(staff.total_sales, staff.sales_count);
            html += `
                <tr>
                    <td>${staff.name}</td>
                    <td><span class="badge bg-secondary">${staff.role}</span></td>
                    <td>${staff.sales_count}</td>
                    <td>₱${staff.total_sales.toLocaleString()}</td>
                    <td>₱${staff.average_sale.toLocaleString()}</td>
                    <td><span class="badge ${performance.badgeClass}">${performance.text}</span></td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    } else {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No staff performance data available</td></tr>';
    }
}

// Update staff chart
function updateStaffChart() {
    const ctx = document.getElementById('staffPerformanceChart').getContext('2d');
    
    if (staffChart) {
        staffChart.destroy();
    }
    
    const totalStaff = staffData.total_staff || 0;
    const staffWithSales = staffData.staff_with_sales || 0;
    const staffWithoutSales = totalStaff - staffWithSales;
    
    staffChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Staff with Sales', 'Staff without Sales'],
            datasets: [{
                data: [staffWithSales, staffWithoutSales],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 99, 132, 0.8)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Get performance level
function getPerformanceLevel(participationRate) {
    if (participationRate >= 80) return 'Excellent';
    if (participationRate >= 60) return 'Good';
    if (participationRate >= 40) return 'Fair';
    return 'Needs Improvement';
}

// Get staff performance level
function getStaffPerformanceLevel(totalSales, salesCount) {
    if (totalSales >= 10000 && salesCount >= 20) {
        return { text: 'Excellent', badgeClass: 'bg-success' };
    } else if (totalSales >= 5000 && salesCount >= 10) {
        return { text: 'Good', badgeClass: 'bg-info' };
    } else if (totalSales >= 1000 && salesCount >= 5) {
        return { text: 'Average', badgeClass: 'bg-warning' };
    } else {
        return { text: 'Below Average', badgeClass: 'bg-danger' };
    }
}

// Get recommendations
function getRecommendations(participationRate, staffWithSales) {
    let recommendations = [];
    
    if (participationRate < 50) {
        recommendations.push('<li class="text-warning"><i class="fas fa-exclamation-triangle"></i> Encourage more staff to participate in sales</li>');
    }
    
    if (staffWithSales < 3) {
        recommendations.push('<li class="text-info"><i class="fas fa-info-circle"></i> Consider sales training for all staff</li>');
    }
    
    if (participationRate >= 80 && staffWithSales >= 5) {
        recommendations.push('<li class="text-success"><i class="fas fa-check-circle"></i> Excellent team participation</li>');
    }
    
    if (recommendations.length === 0) {
        recommendations.push('<li class="text-muted">No specific recommendations at this time</li>');
    }
    
    return recommendations.join('');
}

// Update last updated timestamp
function updateLastUpdated() {
    const now = new Date();
    document.getElementById('lastUpdated').textContent = `Last updated: ${now.toLocaleTimeString()}`;
}

// Refresh data
function refreshData() {
    loadStaffData();
}

// Export data
function exportData() {
    if (Object.keys(staffData).length === 0) {
        alert('No data to export. Please refresh the data first.');
        return;
    }
    
    const dataStr = JSON.stringify(staffData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `staff-analytics-${new Date().toISOString().split('T')[0]}.json`;
    link.click();
    URL.revokeObjectURL(url);
}

// Show error message
function showError(message) {
    const performanceContainer = document.getElementById('teamPerformance');
    const insightsContainer = document.getElementById('performanceInsights');
    
    const errorHtml = `
        <div class="alert alert-danger">
            <h5><i class="fas fa-exclamation-circle"></i> Error</h5>
            <p>${message}</p>
            <button onclick="loadStaffData()" class="btn btn-danger">
                <i class="fas fa-retry"></i> Retry
            </button>
        </div>
    `;
    
    performanceContainer.innerHTML = errorHtml;
    insightsContainer.innerHTML = errorHtml;
}
</script>
@endpush
