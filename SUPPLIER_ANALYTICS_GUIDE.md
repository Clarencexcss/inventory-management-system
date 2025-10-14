# Supplier Analytics Module - Implementation Guide

## 📋 Overview
Complete **Supplier Analytics** feature for ButcherPro that provides comprehensive supplier performance metrics, delivery tracking, and procurement insights using the existing 2 suppliers in the database.

---

## ✅ What Was Implemented

### 1. Database Structure
**Table: `procurements`**
- Stores detailed procurement records for analytics
- Links to existing `suppliers` and `products` tables
- Tracks delivery performance and quality metrics

**Columns:**
- `id` - Primary key
- `supplier_id` - Foreign key to suppliers table
- `product_id` - Foreign key to products table
- `quantity_supplied` - Number of items supplied
- `expected_delivery_date` - When delivery was expected
- `delivery_date` - When delivery actually occurred
- `total_cost` - Total cost of procurement
- `status` - 'on-time' or 'delayed'
- `defective_rate` - Percentage of defective items (0-5%)
- `timestamps` - Created/updated timestamps

**Indexes:** Added for `supplier_id`, `product_id`, `status`, and `delivery_date` for optimal query performance.

---

### 2. Models

#### **Procurement Model** (`app/Models/Procurement.php`)
- Full Eloquent model with relationships
- Belongs to Supplier and Product
- Helper methods:
  - `isOnTime()` - Check if delivery was on time
  - `getDeliveryDelayDays()` - Calculate delay in days
- Proper date casting and decimal precision

#### **Updated Supplier Model** (`app/Models/Supplier.php`)
- Added `procurements()` relationship
- Now has both `purchases()` and `procurements()` relationships

---

### 3. Controller

**`SupplierAnalyticsController`** (`app/Http/Controllers/SupplierAnalyticsController.php`)

**Methods:**

1. **`index()`** - Main dashboard view
   - Loads all analytics data
   - Returns supplier-analytics view

2. **`getSupplierPerformance()`** - Private method
   - Calculates delivery reliability per supplier
   - Computes on-time delivery percentage
   - Calculates average defective rate
   - Computes total costs
   - Generates performance score (0-100)

3. **`getDeliveryTracking()`** - Private method
   - Tracks on-time vs delayed deliveries
   - Calculates percentages

4. **`getProcurementInsights()`** - Private method
   - Total procurement cost
   - Total quantity supplied
   - Average cost per procurement
   - 30-day trend analysis

5. **`getTopSuppliers()`** - Private method
   - Ranks suppliers by total cost
   - Shows procurement count
   - Average defect rate

6. **`getMonthlyProcurementTrends()`** - Private method
   - Monthly cost trends for last 12 months
   - Procurement count per month

7. **`calculatePerformanceScore()`** - Private method
   - 70% weight on on-time delivery
   - 30% weight on quality (low defective rate)
   - Returns score 0-100

8. **`export()`** - Placeholder for future CSV/PDF export

---

### 4. View

**`resources/views/reports/supplier-analytics.blade.php`**

#### **Summary Cards (4 cards):**
1. Total Suppliers
2. On-Time Delivery Percentage
3. Total Procurements
4. Total Procurement Cost

#### **Charts (3 visualizations using Chart.js):**

1. **Monthly Procurement Cost Trends** (Line Chart)
   - Dual Y-axis chart
   - Shows total cost and procurement count over 12 months
   - Interactive tooltips with formatted currency

2. **Delivery Performance** (Doughnut Chart)
   - On-time vs Delayed deliveries
   - Color-coded (Green for on-time, Red for delayed)
   - Shows counts and percentages

3. **Top Suppliers by Total Cost** (Horizontal Bar Chart)
   - Shows top 5 suppliers
   - Ranked by total spending
   - Yellow/warning theme

#### **Insights Panel:**
- Average Cost per Procurement
- Total Quantity Supplied
- 30-Day Trend (up/down indicator)
- Active Suppliers count

#### **Detailed Performance Table:**
Columns:
- Supplier Name
- Total Deliveries
- On-Time % (color-coded badges)
- Average Delay (days)
- Defective % (color-coded badges)
- Total Cost
- Performance Score (progress bar)

**Color Coding:**
- Green: Excellent (≥80%)
- Yellow: Good (60-79%)
- Red: Needs Improvement (<60%)

#### **Action Buttons:**
- Back to Reports
- Print
- Refresh

---

### 5. Seeder

**`SupplierAnalyticsSeeder`** (`database/seeders/SupplierAnalyticsSeeder.php`)

**Data Generation:**
- Uses ONLY the 2 existing suppliers (Magnolia and A.U)
- Generates 2-4 procurement records per supplier per month
- Covers last 12 months
- 80% on-time delivery rate
- Random defective rates (0-5%)
- Realistic quantities (50-300 units)
- Realistic costs (₱5,000-₱150,000)

**Total Records Created:** 66 procurement records

---

### 6. Routes

**Added to `routes/web.php`:**

```php
// Supplier Analytics Routes
Route::get('/reports/supplier-analytics', [SupplierAnalyticsController::class, 'index'])
    ->name('reports.supplier.analytics');
Route::get('/reports/supplier-analytics/export', [SupplierAnalyticsController::class, 'export'])
    ->name('reports.supplier.analytics.export');
```

**Middleware:** `auth`, `role:admin`

---

### 7. Navigation Integration

**Updated `resources/views/reports/index.blade.php`:**
- Changed Supplier Analytics card to link to new analytics page
- Added "Old Report" link to legacy purchases page
- Consistent with Sales Analytics pattern

**Route:** `reports.supplier.analytics`  
**URL:** `http://127.0.0.1:8000/reports/supplier-analytics`

---

## 📊 Analytics Metrics Explained

### Performance Score Calculation
```
Performance Score = (On-Time Rate × 0.7) + ((100 - Defective Rate × 20) × 0.3)
```

**Example:**
- On-Time Rate: 85%
- Defective Rate: 2%
- Score = (85 × 0.7) + ((100 - 2×20) × 0.3) = 59.5 + 18 = **77.5/100**

### Status Badges

**On-Time Delivery:**
- ≥80% = Green (Excellent)
- 60-79% = Yellow (Good)
- <60% = Red (Needs Improvement)

**Defective Rate:**
- ≤2% = Green (Excellent)
- 2-5% = Yellow (Acceptable)
- >5% = Red (Unacceptable)

---

## 🗂️ Files Created/Modified

### Created:
1. `database/migrations/2025_10_14_192444_create_procurements_table.php`
2. `app/Models/Procurement.php`
3. `app/Http/Controllers/SupplierAnalyticsController.php`
4. `resources/views/reports/supplier-analytics.blade.php`
5. `database/seeders/SupplierAnalyticsSeeder.php`
6. `SUPPLIER_ANALYTICS_GUIDE.md` (this file)

### Modified:
1. `app/Models/Supplier.php` - Added `procurements()` relationship
2. `routes/web.php` - Added analytics routes
3. `resources/views/reports/index.blade.php` - Updated supplier analytics card

---

## 🚀 Usage

### Access the Dashboard
1. Login as admin
2. Navigate to **Reports** → **Supplier Analytics**
3. View comprehensive supplier performance metrics

### Navigation Path:
```
Dashboard → Reports → Supplier Analytics
```

### Direct URL:
```
http://127.0.0.1:8000/reports/supplier-analytics
```

---

## 📈 Sample Data

### Existing Suppliers Used:
1. **Magnolia** - Generated 33 procurement records
2. **A.U** - Generated 33 procurement records

### Data Range:
- Last 12 months of procurement data
- 66 total procurement records
- Mix of on-time and delayed deliveries
- Realistic cost and quantity data

---

## 🔧 Technical Details

### Technologies Used:
- **Laravel 10** - Backend framework
- **Chart.js** - Data visualization
- **Bootstrap 5** - UI framework
- **MySQL** - Database
- **Blade Templates** - View engine

### Database Relationships:
```
Supplier (1) ──< (Many) Procurements
Product (1) ──< (Many) Procurements
```

### Query Optimization:
- Eager loading with `with()`
- Database indexes on foreign keys and status
- Efficient aggregation queries
- Grouped queries to minimize database hits

---

## 🎯 Key Features

✅ **Supplier Performance Metrics**
- On-time delivery tracking
- Quality control (defective rate)
- Performance scoring

✅ **Delivery Tracking**
- On-time vs delayed analysis
- Average delay calculation
- Status visualization

✅ **Procurement Insights**
- Monthly spending trends
- Top suppliers ranking
- 30-day trend analysis
- Cost analytics

✅ **Visual Analytics**
- Interactive Chart.js charts
- Color-coded performance indicators
- Progress bars for scores
- Responsive design

✅ **Data Quality**
- Uses only existing suppliers
- Realistic data generation
- Proper date handling
- NULL-safe calculations

---

## 🔄 Future Enhancements

### Recommended Additions:
1. **Export Functionality**
   - PDF export with charts
   - CSV export for raw data
   - Excel export with formatting

2. **Advanced Filters**
   - Date range selection
   - Supplier selection
   - Status filtering

3. **Email Reports**
   - Scheduled supplier performance reports
   - Low-performing supplier alerts

4. **Comparison Tools**
   - Supplier vs supplier comparison
   - Period over period analysis

5. **Integration**
   - Link to supplier management
   - Quick action buttons (email, phone)
   - Procurement request creation

---

## 📝 Notes

- All data is for the **existing 2 suppliers only** (no new suppliers created)
- 80% on-time delivery rate simulates realistic performance
- Performance scores weight on-time delivery more heavily (70%) than quality (30%)
- Charts use Chart.js for consistency with other ButcherPro reports
- Follows ButcherPro design standards and layout patterns

---

## ✨ Testing Checklist

- [x] Migration runs successfully
- [x] Seeder creates data for existing suppliers
- [x] Controller loads without errors
- [x] View renders correctly
- [x] Charts display properly
- [x] Performance calculations are accurate
- [x] Navigation links work
- [x] Back button returns to Reports Dashboard
- [x] Responsive design works on mobile
- [x] Print functionality works

---

## 🎓 Summary

The Supplier Analytics module provides ButcherPro with comprehensive insights into supplier performance, helping make data-driven procurement decisions. The system tracks delivery reliability, quality metrics, and cost trends while maintaining integration with existing supplier and product data.

**Result:** A fully functional, production-ready supplier analytics dashboard that enhances ButcherPro's reporting capabilities! 🚀
