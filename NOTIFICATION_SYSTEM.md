# Admin Notification System

This document describes the admin notification system that has been implemented for the inventory management system.

## Features

- **Real-time notifications** for pending orders and cancelled orders
- **Notification navbar** in the admin interface with unread count badge
- **Clickable notifications** that navigate to order details
- **Mark as read** functionality for individual and all notifications
- **Automatic notification creation** when orders are created or cancelled

## Components

### 1. AdminNotification Model
- Stores notification data including type, title, message, and related order information
- Includes relationships to Order and User models
- Provides methods for marking notifications as read/unread

### 2. AdminNotificationService
- Handles creation of different types of notifications
- Provides methods to get unread counts and recent notifications
- Manages notification statistics

### 3. AdminNotificationNavbar (Livewire Component)
- Displays notification bell icon with unread count badge
- Shows dropdown with recent notifications
- Handles marking notifications as read
- Provides navigation to order details

### 4. AdminNotificationController
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

## Usage

### For Developers

#### Creating Notifications
```php
use App\Services\AdminNotificationService;

$notificationService = app(AdminNotificationService::class);

// Create pending order notification
$notificationService->createPendingOrderNotification($order);

// Create cancelled order notification
$notificationService->createCancelledOrderNotification($order, $cancelledByUser);
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

1. **View Notifications**: Click the bell icon in the admin navbar
2. **Read Notifications**: Click on any notification to view the order details
3. **Mark as Read**: Click "Mark all read" button to mark all notifications as read
4. **Navigate to Orders**: Click "View All Orders" to go to the orders page

## Database Schema

The `admin_notifications` table includes:
- `id`: Primary key
- `type`: Notification type (pending_order, cancelled_order, etc.)
- `title`: Notification title
- `message`: Notification message
- `data`: JSON data with additional information
- `is_read`: Boolean flag for read status
- `read_at`: Timestamp when marked as read
- `order_id`: Foreign key to orders table
- `cancelled_by_user_id`: Foreign key to users table (for cancelled orders)
- `created_at`, `updated_at`: Timestamps

## Routes

- `GET /admin/notifications/unread-count` - Get unread notifications count
- `GET /admin/notifications/recent` - Get recent notifications
- `POST /admin/notifications/{notification}/mark-read` - Mark specific notification as read
- `POST /admin/notifications/mark-all-read` - Mark all notifications as read
- `GET /admin/notifications/stats` - Get notification statistics

## Testing

Use the test command to create sample notifications:
```bash
php artisan test:notifications --count=5
```

## Integration

The notification system is automatically integrated into:
- Order creation (triggers pending order notifications)
- Order cancellation (triggers cancelled order notifications)
- Admin layout (displays notification navbar)

## Future Enhancements

- Real-time updates using WebSockets or Server-Sent Events
- Email notifications for critical events
- Notification preferences and settings
- Push notifications for mobile devices
- Notification categories and filtering
