# Staff Performance Tracking Module

## Overview
The Staff Performance Tracking Module is a comprehensive feature for ButcherPro that enables tracking, evaluation, and analysis of staff performance. It includes automatic performance score calculation, detailed reports, and visual analytics integration.

## Features Implemented

### 1. **Database Structure**

#### Staff Table
- `id` - Primary key
- `name` - Staff member's full name
- `position` - Job position (Butcher, Cashier, etc.)
- `department` - Department (default: Operations)
- `contact_number` - Phone number
- `date_hired` - Date of hiring
- `status` - Active/Inactive status
- `timestamps` - Created and updated dates

#### Staff Performance Table
- `id` - Primary key
- `staff_id` - Foreign key to staff table
- `month` - Evaluation month (YYYY-MM format)
- `attendance_rate` - Attendance percentage (0-100)
- `task_completion_rate` - Task completion percentage (0-100)
- `customer_feedback_score` - Customer feedback score (1-5 scale)
- `overall_performance` - Auto-calculated overall score
- `remarks` - Optional evaluation notes
- `timestamps` - Created and updated dates
- **Unique constraint**: One record per staff per month

### 2. **Performance Calculation Formula**

The overall performance score is automatically calculated using the following weighted formula:

```
Overall Performance = (Attendance × 30%) + (Task Completion × 40%) + (Feedback × 30%)
```

Where:
- Attendance Rate: 0-100% (Weight: 30%)
- Task Completion Rate: 0-100% (Weight: 40%)
- Customer Feedback Score: 1-5 scale converted to percentage (Weight: 30%)

**Performance Grades:**
- 90-100%: Excellent
- 80-89%: Very Good
- 70-79%: Good
- 60-69%: Satisfactory
- Below 60%: Needs Improvement

### 3. **Models**

#### Staff Model (`app/Models/Staff.php`)
- **Relationships:**
  - `performances()` - Has many performance records
  - `latestPerformance()` - Latest performance record
- **Attributes:**
  - `average_performance` - Computed average of all performance scores

#### StaffPerformance Model (`app/Models/StaffPerformance.php`)
- **Auto-calculation:** Performance score is automatically calculated on save
- **Relationships:**
  - `staff()` - Belongs to a staff member
- **Attributes:**
  - `grade` - Performance grade based on score

### 4. **Controllers**

#### StaffController (`app/Http/Controllers/StaffController.php`)
Handles CRUD operations for staff members:
- `index()` - List all staff with performance averages
- `create()` - Show staff creation form
- `store()` - Create new staff member
- `show()` - Display staff details and performance history
- `edit()` - Show staff edit form
- `update()` - Update staff information
- `destroy()` - Delete staff member

#### StaffPerformanceController (`app/Http/Controllers/StaffPerformanceController.php`)
Handles performance evaluations and reports:
- `index()` - List all performance records
- `create()` - Show performance evaluation form
- `store()` - Create new performance record
- `show()` - Display performance details
- `edit()` - Show performance edit form
- `update()` - Update performance record
- `destroy()` - Delete performance record
- `report()` - Generate comprehensive performance report

### 5. **Routes**

All routes are protected with admin authentication (`auth` and `role:admin` middleware):

```php
// Staff Management
GET     /staff                          - List all staff
GET     /staff/create                   - Create staff form
POST    /staff                          - Store new staff
GET     /staff/{staff}                  - Show staff details
GET     /staff/{staff}/edit             - Edit staff form
PUT     /staff/{staff}                  - Update staff
DELETE  /staff/{staff}                  - Delete staff

// Performance Management
GET     /staff-performance              - List all performance records
GET     /staff-performance/create       - Create performance form
POST    /staff-performance              - Store new performance
GET     /staff-performance/{performance} - Show performance details
GET     /staff-performance/{performance}/edit - Edit performance form
PUT     /staff-performance/{performance} - Update performance
DELETE  /staff-performance/{performance} - Delete performance

// Reports
GET     /reports/staff-performance      - Performance report with charts
```

### 6. **Views**

#### Staff Management Views (`resources/views/admin/staff/`)
- `index.blade.php` - Staff listing with performance averages
- `create.blade.php` - Add new staff member
- `edit.blade.php` - Edit staff information
- `show.blade.php` - Staff profile with performance history

#### Performance Views (`resources/views/admin/performance/`)
- `index.blade.php` - Performance records listing
- `create.blade.php` - Add performance evaluation (with live calculation)
- `edit.blade.php` - Edit performance evaluation (with live calculation)
- `show.blade.php` - Detailed performance view
- `report.blade.php` - Comprehensive performance report with charts

### 7. **Seeder**

**StaffPerformanceSeeder** (`database/seeders/StaffPerformanceSeeder.php`)
- Creates 12 dummy staff members with realistic Filipino names
- Generates 3 months of performance data for each staff member
- Randomized but realistic performance metrics
- Automatic performance calculation

**Pre-seeded Staff:**
1. John Dela Cruz - Butcher
2. Maria Santos - Cashier
3. Paolo Reyes - Inventory Clerk
4. Ana Dizon - Supervisor
5. Mark Tan - Delivery Staff
6. Jessica Lim - Cashier
7. Carlo Mendoza - Butcher
8. Ella Robles - Inventory Clerk
9. Nathan Cruz - Cleaner
10. Lea Villanueva - Cashier
11. Rico Bautista - Delivery Staff
12. Tina Ramos - Supervisor

## Performance Report Features

The comprehensive performance report (`/reports/staff-performance`) includes:

### Summary Cards
- Total Staff Count
- Average Performance Percentage
- Evaluation Period (Number of Months)

### Top & Bottom Performers
- **Top 3 Performers** - Highest average performance
- **Bottom 3 Performers** - Staff needing improvement

### Visual Analytics (Chart.js)

#### 1. Staff Performance Bar Chart
- Displays average performance for all staff
- Color-coded bars:
  - Green (≥80%): Excellent performance
  - Yellow (≥60%): Satisfactory performance
  - Red (<60%): Needs improvement

#### 2. Monthly Trend Line Chart
Shows trends over the last 6 months for:
- Overall Performance
- Attendance Rate
- Task Completion Rate
- Customer Feedback (converted to percentage)

### Detailed Metrics Table
Complete table showing:
- Rank
- Staff Name & Position
- Average Performance (visual progress bar)
- Performance Grade
- Quick action buttons

## Integration Points

### 1. Navigation Menu
Added to the main navigation (Admin only):
- Staff menu item in navbar
- Direct access to staff management

### 2. Reports Dashboard
Updated `/reports` page with:
- Staff Performance card
- Link to performance report
- Link to staff management

### 3. Dashboard Analytics
Staff count displayed in analytics dashboard quick stats

## Key Features

### ✅ Auto-Calculation
- Overall performance is automatically calculated when creating or updating records
- Real-time calculation preview in forms using JavaScript
- No manual calculation required

### ✅ Data Validation
- Required fields validation
- Numeric range validation (0-100 for percentages, 1-5 for feedback)
- Unique constraint prevents duplicate evaluations for same staff/month
- Proper error messages and user feedback

### ✅ Responsive Design
- Bootstrap 5 components
- Mobile-friendly tables and forms
- Responsive charts
- Proper card layouts

### ✅ Visual Feedback
- Color-coded performance indicators
- Progress bars for metrics
- Badge system for grades and status
- Interactive charts

### ✅ User Experience
- Breadcrumb navigation
- Success/error alerts
- Confirmation dialogs for deletions
- Quick action buttons
- Helpful tooltips and hints

## Usage Guide

### Adding a Staff Member
1. Navigate to Staff menu
2. Click "Add New Staff"
3. Fill in required information (Name, Position, Status)
4. Optional: Department, Contact Number, Date Hired
5. Click "Save Staff Member"

### Recording Performance
1. Go to Staff Performance or Staff Details
2. Click "Add Performance Record"
3. Select staff member and month
4. Enter metrics:
   - Attendance Rate (0-100%)
   - Task Completion Rate (0-100%)
   - Customer Feedback Score (1-5)
5. View auto-calculated overall score
6. Add optional remarks
7. Click "Save Performance Record"

### Viewing Reports
1. Navigate to Reports > Staff Performance
   OR
2. Go to Staff menu > View Report button
3. View:
   - Summary statistics
   - Top/bottom performers
   - Performance trends
   - Detailed metrics table

### Editing/Deleting Records
- Use action buttons (Edit/Delete) on any listing
- Edit forms pre-populate with existing data
- Delete requires confirmation

## Technical Details

### Technologies Used
- **Backend:** Laravel 10
- **Database:** MySQL
- **Frontend:** Blade Templates, Bootstrap 5
- **Charts:** Chart.js
- **Icons:** Font Awesome
- **JavaScript:** Vanilla JS for calculations

### Files Created/Modified

**Migrations:**
- `2025_10_14_143449_create_staff_table.php`
- `2025_10_14_143458_create_staff_performance_table.php`

**Models:**
- `app/Models/Staff.php`
- `app/Models/StaffPerformance.php`

**Controllers:**
- `app/Http/Controllers/StaffController.php`
- `app/Http/Controllers/StaffPerformanceController.php`

**Views:**
- `resources/views/admin/staff/index.blade.php`
- `resources/views/admin/staff/create.blade.php`
- `resources/views/admin/staff/edit.blade.php`
- `resources/views/admin/staff/show.blade.php`
- `resources/views/admin/performance/index.blade.php`
- `resources/views/admin/performance/create.blade.php`
- `resources/views/admin/performance/edit.blade.php`
- `resources/views/admin/performance/show.blade.php`
- `resources/views/admin/performance/report.blade.php`

**Seeders:**
- `database/seeders/StaffPerformanceSeeder.php`

**Modified Files:**
- `routes/web.php` - Added staff performance routes
- `resources/views/layouts/butcher.blade.php` - Added navigation menu item
- `resources/views/reports/index.blade.php` - Added performance report card
- `database/seeders/DatabaseSeeder.php` - Registered seeder

## Database Commands

```bash
# Run migrations
php artisan migrate

# Seed staff and performance data
php artisan db:seed --class=StaffPerformanceSeeder

# Or seed all data
php artisan db:seed
```

## Future Enhancement Possibilities

1. **Export Features**
   - Export performance reports to PDF
   - Export data to Excel/CSV
   
2. **Advanced Analytics**
   - Performance predictions using trends
   - Comparative analysis between departments
   - Year-over-year comparisons

3. **Notifications**
   - Alert when performance drops
   - Reminder for monthly evaluations
   - Achievement notifications

4. **Goals & Targets**
   - Set performance targets
   - Track goal achievement
   - Performance improvement plans

5. **Integration**
   - Link with payroll system
   - Connect with attendance system
   - Integration with sales data

## Security

- All routes protected with authentication
- Admin-only access (role:admin middleware)
- CSRF protection on all forms
- SQL injection protection via Eloquent ORM
- XSS protection via Blade templating

## Troubleshooting

### Common Issues

**Issue:** Routes not found
- **Solution:** Run `php artisan route:clear` and `php artisan optimize:clear`

**Issue:** Views not rendering
- **Solution:** Run `php artisan view:clear`

**Issue:** Database errors
- **Solution:** Check database connection in `.env` file
- Run `php artisan migrate:fresh --seed` to reset database

**Issue:** Charts not displaying
- **Solution:** Ensure Chart.js is loading (check browser console)
- Verify data is being passed to views

## Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Use `php artisan tinker` to debug models
3. Check browser console for JavaScript errors

## Conclusion

The Staff Performance Tracking Module is now fully integrated into ButcherPro, providing comprehensive tools for managing and evaluating staff performance with automatic calculations, visual analytics, and detailed reporting capabilities.
