# Staff Management UI - Quick Reference Guide

## 🎨 **DESIGN ALIGNMENT CHECKLIST**

### ✅ What Changed
- **Layout**: `layouts.tabler` → `layouts.butcher`
- **Navbar**: Now shows Yannis Meatshop branding with dark red (#8B0000)
- **Tables**: Hover effects, clean white cards, consistent spacing
- **Forms**: Two-column layout, proper validation styling
- **Alerts**: Integrated with ButcherPro's alert system + SweetAlert2 toasts
- **Typography**: Matching page-title style with primary color

### ✅ What Stayed Consistent
- All original functionality intact
- Same routes and URLs
- Database structure unchanged
- Performance calculation logic preserved
- Chart.js integration maintained

---

## 📋 **COMMON UI PATTERNS USED**

### Page Header Pattern
```html
<div class="row mb-4">
    <div class="col">
        <h1 class="page-title">
            <i class="fas fa-users me-2"></i>Page Title
        </h1>
    </div>
    <div class="col-auto">
        <a href="#" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New
        </a>
    </div>
</div>
```

### Table Pattern
```html
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-vcenter card-table mb-0">
                <!-- Table content -->
            </table>
        </div>
    </div>
</div>
```

### Form Card Pattern
```html
<div class="card">
    <form method="POST" action="#">
        @csrf
        <div class="card-body">
            <!-- Form fields -->
        </div>
        <div class="card-footer text-end">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Save
            </button>
            <a href="#" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i>Cancel
            </a>
        </div>
    </form>
</div>
```

### Alert Pattern
```html
<x-alert/>
<!-- Automatically shows success/error messages from session -->
```

### Toast Notification Pattern
```javascript
Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: 'Action completed successfully!',
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});
```

---

## 🎨 **COLOR CODES**

```css
/* ButcherPro Brand Colors */
--primary-color: #8B0000;      /* Dark Red - Main brand */
--secondary-color: #4A0404;    /* Darker Red - Hover states */
--accent-color: #FF4136;       /* Bright Red - Accents */

/* Bootstrap Status Colors */
Success: #28a745;  /* Green - Active, Good performance */
Warning: #ffc107;  /* Yellow - Moderate performance */
Danger: #dc3545;   /* Red - Poor performance, errors */
Info: #17a2b8;     /* Cyan - Info badges */
Secondary: #6c757d; /* Gray - Neutral states */
```

---

## 📊 **PERFORMANCE BADGES**

### Badge Color Rules
```php
@php
    $score = $performance->overall_performance;
    $color = $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
@endphp
<span class="badge bg-{{ $color }}">
    {{ number_format($score, 1) }}%
</span>
```

**Color Mapping:**
- **Green (≥80%)**: Excellent performance
- **Yellow (60-79%)**: Satisfactory performance
- **Red (<60%)**: Needs improvement

---

## 🔔 **NOTIFICATION SYSTEM**

### Session Flash Messages
Used in controllers:
```php
return redirect()
    ->route('staff.index')
    ->with('success', 'Staff member created successfully!');
```

Displays automatically via `<x-alert/>` component.

### SweetAlert2 Toasts
Used in Blade views:
```javascript
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
@endif
```

**Features:**
- Auto-dismiss after 3 seconds
- Progress bar shows remaining time
- Positioned top-right corner
- Non-intrusive overlay

---

## 🎯 **RESPONSIVE BREAKPOINTS**

```css
/* Mobile First Approach */
col-12        /* Full width on mobile */
col-md-6      /* Half width on tablet+ */
col-lg-4      /* Third width on desktop+ */

/* Common Patterns */
.row .col-md-4  /* 3 columns on tablets */
.row .col-md-6  /* 2 columns on tablets */
.row .col-12    /* Full width always */
```

---

## 📱 **MOBILE OPTIMIZATIONS**

- Tables scroll horizontally on small screens
- Forms stack vertically on mobile (col-12 default)
- Buttons maintain spacing with proper margins
- Navigation collapses into hamburger menu
- Cards remain full-width on mobile

---

## 🛠️ **COMMON TASKS**

### Adding a New View
1. Create file in `resources/views/admin/staff/` or `resources/views/admin/performance/`
2. Start with `@extends('layouts.butcher')`
3. Use standard page header pattern
4. Wrap content in `<div class="container-fluid py-4">`
5. Add `<x-alert/>` after header
6. Use card-based layout for content

### Adding Toast Notification
1. Add SweetAlert2 script in `@push('page-scripts')`
2. Include CDN: `<script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>`
3. Add toast code in script section
4. Check for session success/error

### Styling a Table
1. Wrap in `<div class="card">`
2. Card body with `p-0` (no padding)
3. Table classes: `table table-hover table-vcenter card-table mb-0`
4. Add `table-responsive` wrapper for mobile

### Creating a Form
1. Use card structure
2. Form fields in `card-body`
3. Buttons in `card-footer text-end`
4. Add validation error displays
5. Include `@csrf` token

---

## 🎨 **ICON USAGE**

Font Awesome icons used throughout:

```html
<i class="fas fa-users"></i>        <!-- Staff -->
<i class="fas fa-chart-line"></i>   <!-- Performance -->
<i class="fas fa-chart-bar"></i>    <!-- Reports -->
<i class="fas fa-plus"></i>         <!-- Add -->
<i class="fas fa-edit"></i>         <!-- Edit -->
<i class="fas fa-trash"></i>        <!-- Delete -->
<i class="fas fa-eye"></i>          <!-- View -->
<i class="fas fa-save"></i>         <!-- Save -->
<i class="fas fa-times"></i>        <!-- Cancel -->
<i class="fas fa-arrow-left"></i>   <!-- Back -->
<i class="fas fa-trophy"></i>       <!-- Top performer -->
<i class="fas fa-exclamation-triangle"></i> <!-- Warning -->
```

---

## ⚙️ **CACHE CLEARING**

After making changes:
```bash
php artisan view:clear
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

Or all at once:
```bash
php artisan optimize:clear
```

---

## 🔍 **TROUBLESHOOTING**

### Issue: Changes not showing
**Solution:** Clear view cache with `php artisan view:clear`

### Issue: Layout looks broken
**Solution:** Check that you're using `@extends('layouts.butcher')` not `layouts.tabler`

### Issue: Toast not showing
**Solution:** Verify SweetAlert2 is loaded and session has success/error message

### Issue: Table not responsive
**Solution:** Wrap table in `<div class="table-responsive">`

### Issue: Colors don't match
**Solution:** Use ButcherPro color variables or Bootstrap classes (bg-success, bg-warning, etc.)

---

## 📚 **FILE REFERENCE**

**Layout:** `resources/views/layouts/butcher.blade.php`
**Alert Component:** `resources/views/components/alert.blade.php`
**SweetAlert2:** `public/assets/js/sweetalert2.all.min.js`

**Staff Views:**
- Index: `resources/views/admin/staff/index.blade.php`
- Create: `resources/views/admin/staff/create.blade.php`
- Edit: `resources/views/admin/staff/edit.blade.php`
- Show: `resources/views/admin/staff/show.blade.php`

**Performance Views:**
- Index: `resources/views/admin/performance/index.blade.php`
- Create: `resources/views/admin/performance/create.blade.php`
- Edit: `resources/views/admin/performance/edit.blade.php`
- Show: `resources/views/admin/performance/show.blade.php`
- Report: `resources/views/admin/performance/report.blade.php`

---

## ✅ **QUALITY CHECKLIST**

Before committing changes, verify:

- [ ] Using `layouts.butcher` layout
- [ ] Page header follows standard pattern
- [ ] Alert component included (`<x-alert/>`)
- [ ] Tables wrapped in cards with proper classes
- [ ] Forms have validation error displays
- [ ] Buttons use consistent colors and icons
- [ ] Mobile responsive (test on small screen)
- [ ] Toast notifications work (if applicable)
- [ ] Success messages show correctly
- [ ] Color scheme matches ButcherPro
- [ ] Icons are consistent with other pages
- [ ] Spacing matches existing pages (py-4, mb-4, etc.)

---

## 🎉 **DONE!**

All Staff Management views are now perfectly aligned with ButcherPro's design system!

For detailed technical documentation, see:
- `STAFF_PERFORMANCE_MODULE.md` - Complete technical docs
- `STAFF_UI_REFACTORING_SUMMARY.md` - Refactoring details
- `STAFF_PERFORMANCE_QUICK_START.md` - User guide
