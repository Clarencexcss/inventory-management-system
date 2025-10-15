# Staff Notification System - Quick Reference

## 📱 **STAFF NOTIFICATION NAVBAR**

The staff notification navbar is displayed in the top navigation bar for staff users, showing:
- Bell icon with unread count badge
- Dropdown with recent notifications (up to 5)
- "Mark all read" button
- Links to "View All Notifications" and "Orders"

## 📄 **STAFF NOTIFICATIONS PAGE**

Accessible via: `/staff/notifications`

### Features:
- Notification statistics cards
- Full notification list with pagination
- Mark individual notifications as read
- Mark all notifications as read
- View associated orders

## 🔔 **NOTIFICATION TYPES**

### Pending Order (`pending_order`)
- **Trigger**: New order created with PENDING status
- **Icon**: ⏰ Yellow clock
- **Example**: "New order #INV-ABC123 from John Doe is pending approval."

### Cancelled Order (`cancelled_order`)
- **Trigger**: Order is cancelled
- **Icon**: ❌ Red X
- **Example**: "Order #INV-ABC123 from John Doe has been cancelled by Admin."

### Low Stock Alert (`low_stock`)
- **Trigger**: Product stock falls below threshold
- **Icon**: ⚠️ Yellow warning triangle
- **Example**: "Product Beef Sirloin (Code: BEEF001) is running low. Current stock: 5 kg."

## ⚙️ **TECHNICAL COMPONENTS**

### Models
- `App\Models\StaffNotification`

### Services
- `App\Services\StaffNotificationService`

### Controllers
- `App\Http\Controllers\StaffNotificationController`

### Livewire Components
- `App\Livewire\StaffNotificationNavbar`

### Views
- `resources\views\livewire\staff-notification-navbar.blade.php`
- `resources\views\staff\notifications\index.blade.php`

## 🔄 **AUTOMATIC NOTIFICATIONS**

1. **Order Creation**: When a new order is created with PENDING status
2. **Order Cancellation**: When an order is cancelled by admin or customer
3. **Low Stock**: When product inventory falls below threshold (future implementation)

## 🛠️ **MANUAL TESTING**

Create test notifications:
```bash
php artisan test:staff-notifications --count=5
```

View notification statistics:
```bash
php artisan test:staff-notifications --count=0
```

## 📚 **FULL DOCUMENTATION**

See `STAFF_NOTIFICATION_SYSTEM.md` for complete documentation.