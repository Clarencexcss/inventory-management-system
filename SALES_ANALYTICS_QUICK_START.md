# Sales Analytics Module - Quick Start Guide

## ✅ Installation Complete!

The Sales Analytics Module has been successfully implemented in your ButcherPro system.

## 🎯 What's Been Added

### Database
- ✅ `sales_reports` table created (60 records seeded for 2020-2024)
- ✅ `monthly_expenses` table created (60 expense records seeded)
- ✅ `monthly_sales_inputs` table created (ready for 2025 manual input)
- ✅ Automatic profit calculations implemented

### Features
- ✅ **Yearly Trend Analysis** - Compare sales, expenses, and profits from 2020-2025
- ✅ **Monthly Breakdown** - Detailed month-by-month performance
- ✅ **Expense Tracking** - Four expense categories with automatic totaling
- ✅ **Top Products Analysis** - Revenue and profit calculation for best sellers
- ✅ **2025 Manual Input** - Editable monthly sales data entry
- ✅ **Interactive Charts** - 5 Chart.js visualizations
- ✅ **ButcherPro Design** - Consistent theme integration

## 🚀 Quick Access URLs

After starting your server (`php artisan serve`), access:

1. **Sales Analytics Dashboard**
   - URL: `http://localhost:8000/sales-analytics`
   - View all analytics, charts, and trends

2. **Reports Index** (Updated)
   - URL: `http://localhost:8000/reports`
   - New "Sales Analytics" card with link to module

3. **Direct Navigation**
   - Login → Dashboard → Reports → Sales Analytics

## 📊 Sample Data Generated

**2020-2024 Historical Data:**
- 60 sales reports (12 months × 5 years)
- 60 expense records (matching months/years)
- Monthly sales range: ₱90,000 - ₱120,000
- Total ~₱6,000,000 in historical sales

**Expense Categories (Auto-generated):**
- Electricity bill: ₱8,000-₱10,000/month
- Staff salaries: ₱20,000-₱25,000/month
- Product resupply: ₱15,000-₱20,000/month
- Equipment maintenance: ₱5,000-₱8,000/month

## 💰 Key Features

### 1. Performance Overview
View at a glance:
- Gross sales for selected year
- Total expenses for selected year
- Net profit (sales - expenses)
- Profit margin percentage

### 2. Interactive Charts
Five beautiful Chart.js visualizations:

**Yearly Sales Trend (2020-2025)**
- Bar chart comparing sales, expenses, and profit
- Shows 6-year historical perspective

**Monthly Sales (Selected Year)**
- Line chart with three datasets
- Displays gross sales, expenses, and net profit

**Profit Margins Over Time**
- Line chart showing profit percentage
- Helps identify trending patterns

**Expense Breakdown**
- Doughnut chart showing expense categories
- Displays percentage distribution

**Top Products Revenue**
- Horizontal bar chart
- Compares revenue and profit for top sellers

### 3. Top-Selling Products
Automatic calculation shows:
- Product name and category
- Quantity sold (from completed orders)
- Total revenue
- Buying price and selling price
- Profit per unit
- Total profit
- Profit margin %

Color-coded badges:
- 🟢 Green: ≥30% margin (excellent)
- 🟡 Yellow: 15-29% margin (good)
- 🔴 Red: <15% margin (needs attention)

### 4. 2025 Manual Input Section
When viewing year 2025:
- Editable table with all 12 months
- Enter sales amount and optional notes
- Save button for each month
- Auto-creates default expense records
- User tracking for audit trail

## 📝 How to Use

### View Analytics for a Specific Year
1. Navigate to Sales Analytics page
2. Use **Year dropdown** to select 2020-2025
3. All charts and tables update automatically
4. View summary cards at top

### Filter Top Products by Category
1. Use **Product Category dropdown**
2. Select a specific category or "All Categories"
3. Top products table updates with filtered results
4. Chart refreshes with new data

### Enter 2025 Monthly Sales
1. Select **year 2025** from dropdown
2. Scroll to "2025 Monthly Sales Input" section
3. For each month:
   - Enter sales amount (required)
   - Add notes (optional)
   - Click **Save** button
4. Charts automatically update with new data
5. Green success message confirms save

### Analyze Expense Breakdown
1. Click on expense doughnut chart
2. Hover over sections to see amounts
3. View percentage distribution
4. Compare expenses across months

## 🔢 Understanding the Calculations

### Net Profit Formula
```
Net Profit = Gross Sales - Total Expenses
```

### Profit Margin Formula
```
Profit Margin % = (Net Profit ÷ Gross Sales) × 100
```

### Product Profit Formula
```
Profit Per Unit = Selling Price - Buying Price
Total Profit = Profit Per Unit × Quantity Sold
Profit Margin % = (Profit Per Unit ÷ Selling Price) × 100
```

### Expense Categories Auto-Total
```
Total Expenses = Electricity + Salaries + Resupply + Equipment
```

## 🎨 User Interface

### Summary Cards
Four stat cards at the top:
1. **Gross Sales** (Blue) - Total sales for selected year
2. **Total Expenses** (Red) - Sum of all expenses
3. **Net Profit** (Green/Red) - Profit after expenses
4. **Profit Margin** (Blue) - Percentage profitability

### Data Tables
Two comprehensive tables:
1. **Top-Selling Products** - 10 columns with full details
2. **Yearly Summary** - All years 2020-2025 comparison

### Filter Controls
Two dropdown filters:
- Year selection (2020-2025)
- Product category filter

## 🔧 Troubleshooting

### Charts Not Showing
```bash
# Clear browser cache
Ctrl + Shift + R (hard refresh)

# Clear Laravel cache
php artisan optimize:clear
```

### No Data Displayed
```bash
# Re-run seeder
php artisan db:seed --class=SalesAnalyticsSeeder

# Check database
php artisan tinker
>>> App\Models\SalesReport::count()
# Should return 60
```

### 2025 Input Not Saving
1. Verify you're logged in as admin
2. Check the success message appears
3. Refresh the page to see saved data
4. Check `monthly_sales_inputs` table in database

### Server Not Starting
```bash
# Check if port 8000 is already in use
netstat -ano | findstr :8000

# Start on different port
php artisan serve --port=8001
```

## 📚 Technical Details

### Routes
```php
GET  /sales-analytics              // Main dashboard
POST /sales-analytics              // Save 2025 input
PUT  /sales-analytics/{id}         // Update 2025 input
GET  /sales-analytics/expense-breakdown/{year}/{month}  // API endpoint
```

### Models
- `SalesReport` - Yearly/monthly sales aggregation
- `MonthlyExpense` - Expense category breakdown
- `MonthlySalesInput` - 2025 manual data entry

### Controller
- `SalesAnalyticsController` - All business logic

### View
- `resources/views/reports/sales-analytics.blade.php`

## 🎯 Navigation

Access Sales Analytics from:
1. **Reports Dashboard** → "Sales Analytics" card
2. **Admin Menu** → Reports → Sales Analytics
3. **Direct URL**: `http://localhost:8000/sales-analytics`

## ✨ Feature Highlights

- ✅ Fully responsive design (mobile, tablet, desktop)
- ✅ Real-time chart updates when filters change
- ✅ Color-coded profit indicators
- ✅ Currency formatting (₱ Philippine Peso)
- ✅ Percentage calculations with 2 decimal precision
- ✅ Hover effects on cards and charts
- ✅ Professional ButcherPro styling
- ✅ Bootstrap 5 components
- ✅ Font Awesome icons

## 🧪 Test the Module

**Quick Test Checklist:**
1. ✅ Login as admin
2. ✅ Navigate to Sales Analytics
3. ✅ Verify 5 charts display
4. ✅ Change year to 2020, 2021, etc.
5. ✅ Select a product category
6. ✅ Switch to year 2025
7. ✅ Enter sales for January 2025
8. ✅ Click Save and verify success message
9. ✅ Check charts updated with 2025 data
10. ✅ Test on mobile viewport

## 📞 Support

### Documentation
- `SALES_ANALYTICS_MODULE.md` - Complete technical documentation
- `routes/web.php` - Route definitions
- `app/Http/Controllers/SalesAnalyticsController.php` - Business logic

### Logs
- Laravel log: `storage/logs/laravel.log`
- Browser console: F12 → Console tab

### Database Inspection
```bash
php artisan tinker

# Check sales reports
>>> App\Models\SalesReport::count()

# Check expenses
>>> App\Models\MonthlyExpense::count()

# View 2025 inputs
>>> App\Models\MonthlySalesInput::all()
```

---

## 🎉 Success!

Your ButcherPro system now has a complete Sales Analytics Module with:
- 📊 **5 interactive charts** for visual insights
- 💰 **Automated profit calculations** for accurate reporting
- 📈 **6 years of data** (2020-2025) for trend analysis
- ✏️ **Manual 2025 input** for real-time data entry
- 🏆 **Top products ranking** with profit margins
- 🎨 **Professional UI** matching ButcherPro theme
- 📱 **Responsive design** for all devices

**Start analyzing your sales performance now at:**
👉 `http://localhost:8000/sales-analytics`

**Happy analyzing! 🚀📊**
