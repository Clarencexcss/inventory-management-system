# Customer Account Deactivation Feature

## Overview
This feature allows customers to deactivate their accounts from the profile settings page. When deactivated, the account is marked as inactive and soft deleted, preventing further access while preserving order history.

## Implementation Details

### 1. Database Migration
- Added migration to add `role`, `status`, and `deleted_at` columns to the `customers` table
- Migration file: `2025_10_29_200024_add_role_and_status_to_customers_table.php`

### 2. Customer Model
- Added `SoftDeletes` trait to enable soft deletion functionality
- Added `deleted_at` to the `$casts` array

### 3. Customer Profile View
- Added account deactivation section to `resources/views/customer/profile.blade.php`
- Includes warning information about consequences of deactivation
- Modal confirmation dialog for additional safety
- Password confirmation requirement

### 4. Controller Method
- Added `deactivate` method to `CustomerController`
- Validates password before proceeding
- Checks for pending orders (prevents deactivation if any exist)
- Updates customer status to 'inactive'
- Soft deletes the customer record
- Logs out the customer after deactivation

### 5. Route
- Added DELETE route for account deactivation: `/customer/profile/deactivate`
- Named route: `customer.profile.deactivate`

## Security Features
- Password confirmation required
- Pending order check prevents deactivation when orders are in progress
- Soft delete preserves data for record-keeping
- Clear warnings about permanence of action

## User Experience
- Clear explanation of deactivation consequences
- Modal confirmation for additional safety
- Success/error messages for feedback
- Redirect to login page after successful deactivation