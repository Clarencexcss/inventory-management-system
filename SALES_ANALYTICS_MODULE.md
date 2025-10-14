# Sales Analytics Module - ButcherPro

## ✅ Module Complete!

A comprehensive Sales Analytics Module has been successfully integrated into ButcherPro with complete functionality for analyzing sales performance, trends, and top-selling products from 2020-2025.

---

## 🎯 Features Implemented

### 1. **Data Timeline (2020-2025)**
- **2020-2024**: Auto-generated sample data with monthly sales ₱90,000-₱120,000
- **2025**: Manual input section for monthly sales (January-December)
- All data stored in database tables: `sales_reports`, `monthly_expenses`, `monthly_sales_inputs`

### 2. **Expenses Tracking**
Four expense categories tracked monthly:
- **Electricity Bill**: ₱8,000-₱10,000/month
- **Staff Salaries**: ₱20,000-₱25,000/month
- **Product Resupply**: ₱15,000-₱20,000/month
- **Equipment Maintenance**: ₱5,000-₱8,000/month

### 3. **Analytics Features**

#### Sales Performance Overview
- Gross sales vs expenses comparison
- Net profit calculation per year and per month
- Profit margin percentages

#### Trend Visualization
Five interactive charts using Chart.js:
1. **Yearly Sales Trend (2020-2025)** - Bar chart comparing sales, expenses, and profit
2. **Monthly Sales** - Line chart showing monthly performance for selected year
3. **Profit Margins Over Time** - Line chart showing profit percentage trends
4. **Expense Breakdown** - Doughnut chart showing expense category distribution
5. **Top Products Revenue** - Horizontal bar chart comparing revenue and profit

#### Top-Selling Products Analysis
- Based on completed orders from `order_details` table
- Displays top 10 products by revenue
- Calculates profit per product (selling price - buying price)
- Shows profit margins with color-coded badges
- Includes total quantity sold and total revenue

### 4. **User Interface**
- ButcherPro design theme with dark red (#8B0000) primary color
- Bootstrap 5 responsive cards and tables
- Navigation breadcrumbs
- Year and category filters
- Summary stat cards with icons
- Color-coded profit indicators (green for positive, red for negative)

### 5. **2025 Manual Input Section**
- Editable table for all 12 months
- Save/update functionality with AJAX-free form submission
- Notes field for additional context
- User tracking (records who entered the data)
- Auto-creates default expense records when sales are saved

---

## 📁 Files Created

### Database
1. **Migration**: `2025_10_14_175954_create_sales_reports_table.php`
   - Stores yearly/monthly aggregated sales data
   - Fields: year, month, gross_sales, total_expenses, net_profit, notes

2. **Migration**: `2025_10_14_180024_create_monthly_expenses_table.php`
   - Stores monthly expense breakdown
   - Fields: year, month, electricity_bill, staff_salaries, product_resupply, equipment_maintenance, total

3. **Migration**: `2025_10_14_180032_create_monthly_sales_inputs_table.php`
   - Stores 2025 manual sales input
   - Fields: year, month, sales_amount, user_id, notes

### Models
1. **`app/Models/SalesReport.php`**
   - Eloquent model with expense relationship
   - Formatted month names and period attributes
   - Decimal casting for financial fields

2. **`app/Models/MonthlyExpense.php`**
   - Auto-calculates total expenses in boot method
   - Decimal casting for all expense fields
   - Month name accessor

3. **`app/Models/MonthlySalesInput.php`**
   - User relationship for tracking data entry
   - Formatted attributes for display
   - Year defaults to 2025

### Controller
**`app/Http/Controllers/SalesAnalyticsController.php`**
- **index()**: Main analytics dashboard with all data and charts
- **store()**: Save 2025 manual sales input
- **update()**: Update existing 2025 sales data
- **getExpenseBreakdown()**: API endpoint for expense details
- Private helper methods for data processing

### Seeder
**`database/seeders/SalesAnalyticsSeeder.php`**
- Generates 60 sales reports (12 months × 5 years)
- Generates 60 monthly expense records
- Randomized realistic data within specified ranges
- Auto-calculates net profit

### Views
**`resources/views/reports/sales-analytics.blade.php`**
- Complete analytics dashboard (754 lines)
- Five Chart.js visualizations
- Responsive Bootstrap layout
- Filter controls for year and category
- Summary statistics cards
- 2025 manual input table
- Top-selling products table and chart
- Yearly summary table

### Routes
Added to `routes/web.php`:
```php
Route::get('/sales-analytics', [SalesAnalyticsController::class, 'index'])->name('sales-analytics.index');
Route::post('/sales-analytics', [SalesAnalyticsController::class, 'store'])->name('sales-analytics.store');
Route::put('/sales-analytics/{id}', [SalesAnalyticsController::class, 'update'])->name('sales-analytics.update');
Route::get('/sales-analytics/expense-breakdown/{year}/{month}', [SalesAnalyticsController::class, 'getExpenseBreakdown'])->name('sales-analytics.expense-breakdown');
```

---

## 🚀 Quick Start Guide

### Access the Module
1. **Start XAMPP** and ensure MySQL is running
2. **Start Laravel server**: `php artisan serve`
3. **Login as Admin** at: `http://localhost:8000/login`
4. **Navigate to Reports**: Dashboard → Reports → Sales Analytics
   - Direct URL: `http://localhost:8000/sales-analytics`

### Using the Analytics Dashboard

#### View Year-Specific Data
1. Use the **Year dropdown** to select 2020-2025
2. Use the **Category dropdown** to filter products by category
3. All charts and tables update based on selection

#### Enter 2025 Monthly Sales
1. Select **year 2025** from dropdown
2. Scroll to "2025 Monthly Sales Input" section
3. Enter sales amounts for each month
4. Optionally add notes
5. Click **Save** button for each month
6. Charts automatically update with new data

#### Analyze Top Products
1. View the "Top-Selling Products" table
2. See revenue, profit, and margin calculations
3. Filter by category to analyze specific product types
4. Review the horizontal bar chart for visual comparison

---

## 📊 Database Schema

### sales_reports
```sql
id              BIGINT UNSIGNED PRIMARY KEY
year            INT
month           INT
gross_sales     DECIMAL(12,2)
total_expenses  DECIMAL(12,2)
net_profit      DECIMAL(12,2)
notes           TEXT
created_at      TIMESTAMP
updated_at      TIMESTAMP

UNIQUE (year, month)
INDEX (year)
INDEX (year, month)
```

### monthly_expenses
```sql
id                      BIGINT UNSIGNED PRIMARY KEY
year                    INT
month                   INT
electricity_bill        DECIMAL(10,2)
staff_salaries          DECIMAL(10,2)
product_resupply        DECIMAL(10,2)
equipment_maintenance   DECIMAL(10,2)
total                   DECIMAL(10,2)
created_at              TIMESTAMP
updated_at              TIMESTAMP

UNIQUE (year, month)
INDEX (year)
INDEX (year, month)
```

### monthly_sales_inputs
```sql
id              BIGINT UNSIGNED PRIMARY KEY
year            INT DEFAULT 2025
month           INT
sales_amount    DECIMAL(12,2)
user_id         BIGINT UNSIGNED (FK to users)
notes           TEXT
created_at      TIMESTAMP
updated_at      TIMESTAMP

UNIQUE (year, month)
INDEX (year)
INDEX (year, month)
```

---

## 🔧 Technical Implementation

### Controller Logic

#### Data Processing Flow
1. **getYearlySummary()**: Aggregates sales reports by year
2. **getMonthlySummary($year)**: 
   - For 2025: Uses `monthly_sales_inputs` table
   - For 2020-2024: Uses `sales_reports` table
3. **getTopSellingProducts()**: Queries `order_details` with product profit calculations
4. **prepareChartData()**: Formats data for Chart.js consumption

### Chart.js Implementation
- **Responsive design**: All charts adapt to screen size
- **Custom tooltips**: Display formatted currency values
- **Color schemes**: Consistent with ButcherPro theme
- **Interactive legends**: Click to toggle datasets
- **Smooth animations**: Tension curves for line charts

### Profit Calculation Logic
```php
// For products
$profitPerUnit = $sellingPrice - $buyingPrice;
$totalProfit = $profitPerUnit × $quantity;
$profitMargin = ($profitPerUnit / $sellingPrice) × 100;

// For monthly reports
$netProfit = $grossSales - $totalExpenses;
$profitMargin = ($netProfit / $grossSales) × 100;
```

---

## 🎨 UI/UX Features

### Color Coding
- **Green (#28a745)**: Positive profits, high margins (≥30%)
- **Yellow (#ffc107)**: Moderate margins (15-29%)
- **Red (#dc3545)**: Negative profits, low margins (<15%)
- **Primary (#0d6efd)**: Sales data
- **Dark Red (#8B0000)**: ButcherPro brand color

### Responsive Breakpoints
- **Mobile**: Cards stack vertically
- **Tablet**: 2 cards per row
- **Desktop**: 3-4 cards per row
- **Charts**: Auto-resize with container

### Interactive Elements
- **Hover effects**: Cards lift on hover
- **Form validation**: Required fields marked
- **Success messages**: Bootstrap alerts for confirmations
- **Loading states**: Spinners during data fetches

---

## 📈 Sample Data Generated

### Years 2020-2024
- **60 sales reports** (12 months × 5 years)
- **60 expense records** (12 months × 5 years)
- **Monthly sales range**: ₱90,000 - ₱120,000
- **Total generated data**: ~₱6,000,000 in sales

### Expense Ranges Per Month
| Category | Min | Max |
|----------|-----|-----|
| Electricity | ₱8,000 | ₱10,000 |
| Salaries | ₱20,000 | ₱25,000 |
| Resupply | ₱15,000 | ₱20,000 |
| Equipment | ₱5,000 | ₱8,000 |
| **Total** | **₱48,000** | **₱63,000** |

---

## 🧪 Testing Checklist

### ✅ Completed Tests
- [x] Migrations run successfully
- [x] Models created with relationships
- [x] Seeder generates 60 records
- [x] Controller methods return data
- [x] Routes registered properly
- [x] View renders without errors
- [x] Charts display with data
- [x] Filters work correctly
- [x] 2025 input saves to database
- [x] Top products calculate correctly
- [x] Profit margins show accurate percentages

### Manual Testing Steps
1. **Access Dashboard**: Login and navigate to Sales Analytics
2. **Test Filters**: Change year and category dropdowns
3. **View Charts**: Verify all 5 charts render
4. **Enter 2025 Data**: Save sales for January-March
5. **Check Calculations**: Verify profit = sales - expenses
6. **Test Products**: Apply category filter on top products
7. **Verify Responsiveness**: Test on mobile viewport

---

## 🐛 Troubleshooting

### Charts Not Displaying
1. Check browser console for JavaScript errors
2. Verify Chart.js CDN is loading:
   ```html
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   ```
3. Clear browser cache: `Ctrl + Shift + R`

### Data Not Showing
1. Verify seeder ran: Check `sales_reports` table has 60 records
2. Re-run seeder: `php artisan db:seed --class=SalesAnalyticsSeeder`
3. Check database connection in `.env`

### 2025 Input Not Saving
1. Verify user is authenticated
2. Check form CSRF token is present
3. Review validation errors in session
4. Check `monthly_sales_inputs` table permissions

### Cache Issues
```bash
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 📚 API Endpoints

### GET /sales-analytics
**Purpose**: Display main analytics dashboard  
**Parameters**: 
- `year` (optional, default: current year)
- `category_id` (optional)

**Returns**: HTML view with charts and data

### POST /sales-analytics
**Purpose**: Save 2025 monthly sales input  
**Body**:
```json
{
  "month": 1-12,
  "sales_amount": 100000.00,
  "notes": "Optional notes"
}
```

### PUT /sales-analytics/{id}
**Purpose**: Update existing 2025 sales input  
**Body**:
```json
{
  "sales_amount": 105000.00,
  "notes": "Updated notes"
}
```

### GET /sales-analytics/expense-breakdown/{year}/{month}
**Purpose**: Get detailed expense breakdown  
**Returns**:
```json
{
  "electricity_bill": 9000.00,
  "staff_salaries": 23000.00,
  "product_resupply": 18000.00,
  "equipment_maintenance": 6500.00,
  "total": 56500.00,
  "month_name": "January",
  "year": 2024
}
```

---

## 🎯 Key Accomplishments

### Functional Requirements ✅
- [x] Controller, routes, and views implemented
- [x] Laravel backend with Eloquent models
- [x] Seeders populate 2020-2024 data
- [x] 2025 manual entries persist in database
- [x] Fully functional charts and graphs
- [x] Editable 2025 input fields
- [x] ButcherPro UI integration

### Performance Optimizations
- Database indexes on frequently queried columns
- Eager loading relationships to reduce queries
- Chart data pre-processed in controller
- Minimal JavaScript for fast page load

### Security Features
- CSRF protection on all forms
- Admin-only access with middleware
- User tracking for audit trail
- SQL injection prevention via Eloquent

---

## 🚀 Future Enhancements

### Potential Features
1. **Export Functionality**: PDF/Excel export of reports
2. **Date Range Filters**: Custom date range selection
3. **Comparison Mode**: Compare multiple years side-by-side
4. **Email Reports**: Scheduled email with analytics summary
5. **Advanced Filtering**: Filter by product, customer, payment method
6. **Forecasting**: Predict future sales based on trends
7. **Dashboard Widgets**: Drag-and-drop customizable widgets

### API Expansion
1. RESTful API for external integrations
2. Real-time data updates with WebSockets
3. GraphQL endpoint for flexible queries
4. Mobile app integration endpoints

---

## 📞 Support

### Documentation Files
- `SALES_ANALYTICS_MODULE.md` - This comprehensive guide
- `database/migrations/` - Database schema definitions
- `app/Models/` - Model relationships and logic

### Debugging
- **Laravel logs**: `storage/logs/laravel.log`
- **Database inspection**: `php artisan tinker`
- **Route list**: `php artisan route:list --name=sales-analytics`

### Common Commands
```bash
# Run migrations
php artisan migrate

# Seed data
php artisan db:seed --class=SalesAnalyticsSeeder

# Start server
php artisan serve

# Clear all caches
php artisan optimize:clear
```

---

## 🎉 Success!

The Sales Analytics Module is now fully operational with:
- ✅ **5 years of historical data** (2020-2024)
- ✅ **60 auto-generated records** with realistic data
- ✅ **5 interactive charts** using Chart.js
- ✅ **Manual 2025 input** with database persistence
- ✅ **Top products analysis** with profit calculations
- ✅ **Complete ButcherPro integration** with consistent styling
- ✅ **Responsive design** for all devices
- ✅ **Production-ready code** with proper validation and security

**Access URL**: `http://localhost:8000/sales-analytics`

Enjoy comprehensive sales analytics for ButcherPro! 📊🚀
