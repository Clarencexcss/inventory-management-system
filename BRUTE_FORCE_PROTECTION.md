# Brute Force Protection Implementation

## Overview
This implementation adds brute force protection to both customer and admin/staff login systems. After 3 failed login attempts, the account will be locked for 5 minutes.

## Features Implemented

### 1. Database Migration
- Created `login_attempts` table to track all login attempts
- Fields: email, ip_address, user_type, attempted_at, successful

### 2. Models
- `App\Models\LoginAttempt` - Model for tracking login attempts
- Enhanced `App\Models\CustomerAuthLog` with additional scopes

### 3. Services
- Modified `App\Services\CustomerAuthService` to:
  - Check for locked accounts before authentication
  - Log failed and successful login attempts
  - Implement lockout mechanism (3 attempts, 5-minute ban)

- Created `App\Services\AdminAuthService` to:
  - Check for locked accounts
  - Log failed and successful login attempts
  - Implement lockout mechanism (3 attempts, 5-minute ban)

### 4. Controllers
- Modified `App\Http\Controllers\Customer\WebAuthController` to properly display lockout messages

### 5. Requests
- Modified `App\Http\Requests\Auth\LoginRequest` to:
  - Check for locked admin accounts before authentication
  - Log failed and successful login attempts

### 6. Views
- Updated customer login view to properly display error messages

## How It Works

### Customer Login
1. When a customer tries to log in, the system first checks if the account is locked
2. If locked (3 failed attempts within 5 minutes), access is denied with a lockout message
3. If not locked, authentication proceeds normally
4. Failed attempts are logged in the `login_attempts` table
5. Successful attempts are also logged
6. After a successful login, the failed attempt counter is effectively reset

### Admin/Staff Login
1. Similar process to customer login
2. Uses the same `login_attempts` table but with `user_type` = 'admin'
3. Lockout mechanism is identical (3 attempts, 5-minute ban)

## Security Features
- Tracks both email and IP address for each attempt
- Differentiates between customer and admin attempts
- Automatic unlock after 5 minutes
- Logs all attempts for audit purposes
- Works with both email and username login for customers

## Testing
- Created comprehensive tests for both customer and admin login lockout functionality
- Tests cover:
  - Account lockout after 3 failed attempts
  - Automatic unlock after 5 minutes
  - Successful login resetting the counter