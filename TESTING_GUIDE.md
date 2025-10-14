# Quick Testing Guide - Image Upload Fixes

## ✅ What Was Fixed

1. **Product Image Upload** - Images now save correctly when creating/editing products
2. **GCash Receipt Upload** - Receipt images now save correctly during customer checkout

---

## 🧪 Testing Steps

### Test 1: Product Image Upload (Admin/Staff)

1. **Login** as admin/staff user
2. **Navigate** to: Products → Create Product
3. **Fill in the form**:
   - Product Name: Test Product Image
   - Select Category
   - Select Unit
   - Select Meat Cut
   - Fill in prices, quantities, etc.
   - **Upload an image** (JPG, PNG, or WEBP - max 2MB)
4. **Submit** the form
5. **Verify**:
   - ✓ Product created successfully
   - ✓ Image appears in product list
   - ✓ Image appears in product details (admin side)
   - ✓ Check database: `SELECT product_image FROM products WHERE name = 'Test Product Image'` - should NOT be NULL
   - ✓ Image file exists: `storage/app/public/products/[filename]`

6. **Test Customer View**:
   - Logout from admin
   - Login as customer
   - Browse products
   - ✓ Verify the test product image appears in catalog
   - ✓ Click product to view details
   - ✓ Verify image appears on product detail page

---

### Test 2: GCash Receipt Upload (Customer)

1. **Login** as customer
2. **Add products** to cart (any product with available stock)
3. **Go to checkout**
4. **Fill in checkout form**:
   - Delivery Address: Your test address
   - Contact Phone: Your test phone
   - **Select Payment Method**: GCash (radio button)
   - **GCash Reference Number**: TEST123456789
   - **Upload GCash Receipt**: Upload a test image (JPG, PNG - max 2MB)
5. **Submit** the order
6. **Verify as Customer**:
   - ✓ Order created successfully
   - ✓ Go to My Orders
   - ✓ Click on the order you just created
   - ✓ Verify GCash reference number is displayed
   - ✓ Verify receipt image is displayed

7. **Verify as Admin**:
   - Logout from customer
   - Login as admin/staff
   - Navigate to Orders
   - Find the test order (should be at the top - most recent)
   - Click to view order details
   - ✓ Verify GCash reference number is shown
   - ✓ Verify receipt image is displayed
   - ✓ Check database: `SELECT proof_of_payment FROM orders WHERE invoice_no = '[your order invoice]'` - should NOT be NULL
   - ✓ Image file exists: `storage/app/public/gcash_receipts/[filename]`

---

## 🔍 What to Check in Database

### For Product Images:
```sql
-- Check if product image was saved
SELECT id, name, code, product_image 
FROM products 
ORDER BY created_at DESC 
LIMIT 5;
```

### For GCash Receipts:
```sql
-- Check if GCash receipt was saved
SELECT id, invoice_no, payment_type, gcash_reference, proof_of_payment 
FROM orders 
WHERE payment_type = 'gcash'
ORDER BY created_at DESC 
LIMIT 5;
```

---

## 📁 File Locations

### Product Images:
- **Storage Location**: `storage/app/public/products/[filename]`
- **Public URL**: `http://localhost/storage/products/[filename]`
- **Database Field**: `products.product_image`

### GCash Receipts:
- **Storage Location**: `storage/app/public/gcash_receipts/[filename]`
- **Public URL**: `http://localhost/storage/gcash_receipts/[filename]`
- **Database Field**: `orders.proof_of_payment`

---

## ⚠️ Common Issues & Solutions

### Issue: "404 Not Found" when viewing image
**Solution**: Run `php artisan storage:link` (already done)

### Issue: Upload fails silently
**Solutions**:
1. Check PHP upload limits in php.ini
2. Check file size (max 2MB for both)
3. Check file format (JPG, PNG, WEBP for products; JPG, PNG, GIF, SVG for receipts)
4. Check storage folder permissions

### Issue: Image saves but doesn't display
**Solutions**:
1. Clear browser cache
2. Check browser console for 404 errors
3. Verify file exists: `dir storage\app\public\products`
4. Verify symbolic link: `dir public\storage`

---

## ✨ Features

### Product Images:
- Supports: JPG, PNG, WEBP
- Max size: 2MB
- Optional field (can create products without images)
- Auto-generates unique filename
- Displays placeholder if no image

### GCash Receipts:
- Supports: JPG, PNG, GIF, SVG
- Max size: 2MB
- Only required when payment method is GCash
- Stores in separate directory for organization
- Visible to both admin and customer

---

## 📝 Notes

- All changes are backwards compatible
- Existing products without images will show placeholders
- Existing orders without receipts will show "No image uploaded"
- Images are stored with unique filenames to prevent conflicts
- Storage directories are automatically created

---

## ✅ Completion Checklist

- [x] Product image upload functionality fixed
- [x] Product images display on admin side
- [x] Product images display on customer side
- [x] GCash receipt upload functionality fixed
- [x] GCash receipts display on admin order details
- [x] GCash receipts display on customer order details
- [x] Storage symbolic link created
- [x] Storage directories created
- [x] Database schema supports both features
- [x] Documentation created

---

**Status**: ✅ READY FOR TESTING
**Date**: October 14, 2025
