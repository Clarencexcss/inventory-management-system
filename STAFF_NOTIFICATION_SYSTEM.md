# Staff Notification System

This document describes the staff notification system that has been implemented for the inventory management system, separate from the admin notification system.

## Features

- **Real-time notifications** for pending orders, cancelled orders, and low stock alerts
- **Notification navbar** in the staff interface with unread count badge
- **Clickable notifications** that navigate to order details
- **Mark as read** functionality for individual and all notifications
- **Automatic notification creation** when orders are created or cancelled

## Components

### 1. StaffNotification Model
- Stores notification data including type, title, message, and related order information
- Includes relationships to Order and User models
- Provides methods for marking notifications as read/unread

### 2. StaffNotificationService
- Handles creation of different types of notifications
- Provides methods to get unread counts and recent notifications
- Manages notification statistics

### 3. StaffNotificationNavbar (Livewire Component)
- Displays notification bell icon with unread count badge
- Shows dropdown with recent notifications
- Handles marking notifications as read
- Provides navigation to order details

### 4. StaffNotificationController
- API endpoints for notification management
- Handles AJAX requests from the frontend

## Notification Types

### Pending Orders
- **Type**: `pending_order`
- **Trigger**: When a new order is created with PENDING status
- **Content**: Shows customer name, order number, and total amount

### Cancelled Orders
- **Type**: `cancelled_order`
- **Trigger**: When an order is cancelled (by customer or admin)
- **Content**: Shows who cancelled the order and cancellation reason

### Low Stock Alerts
- **Type**: `low_stock`
- **Trigger**: When product stock falls below threshold
- **Content**: Shows product name, code, and current stock level

## Routes

The staff notification system is accessible via the following routes:

- `GET /staff/notifications` - View all staff notifications
- `GET /staff/notifications/unread-count` - Get unread notification count
- `GET /staff/notifications/recent` - Get recent notifications
- `POST /staff/notifications/{notification}/mark-read` - Mark a notification as read
- `POST /staff/notifications/mark-all-read` - Mark all notifications as read
- `GET /staff/notifications/stats` - Get notification statistics

## Usage

### For Developers

#### Creating Notifications
```php
use App\Services\StaffNotificationService;

$notificationService = app(StaffNotificationService::class);

// Create pending order notification
$notificationService->createPendingOrderNotification($order);

// Create cancelled order notification
$notificationService->createCancelledOrderNotification($order, $cancelledByUser);

// Create low stock notification
$notificationService->createLowStockNotification($product);
```

#### Getting Notification Data
```php
// Get unread count
$unreadCount = $notificationService->getUnreadCount();

// Get recent notifications
$notifications = $notificationService->getRecentNotifications(10);

// Get unread notifications
$unreadNotifications = $notificationService->getUnreadNotifications();
```

### For Users

1. **View Notifications**: Click the bell icon in the staff navbar
2. **Read Notifications**: Click on any notification to view the order details
3. **Mark as Read**: Click "Mark all read" button to mark all notifications as read
4. **Navigate to Orders**: Click "View All Orders" to go to the orders page

## Database Schema

The `staff_notifications` table includes:
- `id`: Primary key
- `type`: Notification type (pending_order, cancelled_order, low_stock, etc.)
- `title`: Notification title
- `message`: Notification message
- `data`: JSON data with additional information
- `is_read`: Boolean flag for read status
- `read_at`: Timestamp when marked as read
- `order_id`: Foreign key to orders table
- `cancelled_by_user_id`: Foreign key to users table (for cancelled orders)
- `created_at`, `updated_at`: Timestamps

## Testing

### Create Test Notifications
```bash
# Create 5 test notifications
php artisan test:staff-notifications --count=5
```

### View Notification Stats
```bash
# View notification statistics
php artisan test:staff-notifications --count=0
```

## Future Enhancements

- Real-time updates using WebSockets or Server-Sent Events
- Email notifications for critical events
- Notification preferences and settings
- Push notifications for mobile devices
- Notification categories and filtering