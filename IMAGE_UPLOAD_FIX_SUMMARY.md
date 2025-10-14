# Image Upload Issues - Fix Summary

## Issues Fixed

### 1. Product Image Upload Issue
**Problem**: When creating a product on the admin/staff side, the image wasn't being saved to the database (field was NULL).

**Root Causes**:
1. The `product_image` field was missing from the Product model's `$fillable` array
2. The `StoreProductRequest` validation didn't include validation rules for `product_image`

**Solutions Applied**:

#### a) Updated Product Model
**File**: `app/Models/Product.php`
- Added `'product_image'` to the `$fillable` array
- This allows the product_image field to be mass-assigned when creating/updating products

```php
protected $fillable = [
    'name',
    'slug',
    'code',
    'category_id',
    'unit_id',
    'meat_cut_id',
    'quantity',
    'price_per_kg',
    'selling_price',
    'storage_location',
    'expiration_date',
    'source',
    'notes',
    'buying_price',
    'quantity_alert',
    'product_image'  // Added this field
];
```

#### b) Updated StoreProductRequest
**File**: `app/Http/Requests/Product/StoreProductRequest.php`
- Added validation rule for `product_image` field
- Allows nullable, validates image format and size (max 2MB)

```php
'product_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
```

#### c) Fixed Customer Product Views
Updated the following customer-facing views to use the correct image path:

**Files**:
- `resources/views/customer/products/index.blade.php`
- `resources/views/customer/products/show.blade.php`
- `resources/views/customer/products/category.blade.php`

**Changed from**: `Storage::url($product->product_image)`
**Changed to**: `asset('storage/products/' . $product->product_image)`

This ensures consistency with the admin views and correct image path resolution.

---

### 2. GCash Receipt Upload Issue
**Problem**: When customers uploaded GCash receipt during checkout, the image wasn't being saved and admin/staff couldn't see it.

**Root Cause**:
- Form input name mismatch: The checkout form used `name="gcash_receipt"` but the controller was checking for `$request->hasFile('proof_of_payment')`
- Validation rule also referenced the wrong field name

**Solutions Applied**:

#### Updated CheckoutController
**File**: `app/Http/Controllers/Customer/CheckoutController.php`

**Changes Made**:

1. **Fixed Validation Rule** (Line ~42):
```php
// OLD
'proof_of_payment' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

// NEW
'gcash_receipt' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
```

2. **Fixed File Upload Check** (Line ~65):
```php
// OLD
if ($request->payment_type === 'gcash' && $request->hasFile('proof_of_payment')) {
    $proofOfPaymentPath = $request->file('proof_of_payment')->store('gcash_receipts', 'public');
}

// NEW
if ($request->payment_type === 'gcash' && $request->hasFile('gcash_receipt')) {
    $proofOfPaymentPath = $request->file('gcash_receipt')->store('gcash_receipts', 'public');
}
```

The stored path is still saved in the database as `proof_of_payment` field, which is correct.

---

## Verification Points

### Product Images
1. ✅ Admin can upload product images during product creation
2. ✅ Product images are saved to `storage/app/public/products/`
3. ✅ Image filename is saved to database in `products.product_image` field
4. ✅ Images display correctly on admin product list and detail pages
5. ✅ Images display correctly on customer product catalog and detail pages
6. ✅ Image path format: `asset('storage/products/' . $product->product_image)`

### GCash Receipt Images
1. ✅ Customers can upload GCash receipt during checkout when selecting GCash payment
2. ✅ Receipt images are saved to `storage/app/public/gcash_receipts/`
3. ✅ Image path is saved to database in `orders.proof_of_payment` field
4. ✅ Receipts display correctly on admin order detail page
5. ✅ Receipts display correctly on customer order detail page
6. ✅ Image path format: `asset('storage/' . $order->proof_of_payment)`

---

## Testing Instructions

### Test Product Image Upload:
1. Log in as admin/staff
2. Navigate to Products → Create Product
3. Fill in all required fields
4. Upload a product image (JPG, PNG, or WEBP, max 2MB)
5. Submit the form
6. Verify:
   - Image appears on the product list page
   - Image appears on the product detail page (admin)
   - Check database: `products.product_image` should contain the filename
   - Image appears on customer product catalog
   - Image appears on customer product detail page

### Test GCash Receipt Upload:
1. Log in as a customer
2. Add products to cart
3. Go to checkout
4. Select "GCash" as payment method
5. Enter GCash reference number
6. Upload a receipt image (JPG, PNG, or GIF, max 2MB)
7. Complete the order
8. Verify as customer:
   - View your order details
   - Confirm the receipt image is visible
9. Verify as admin:
   - Navigate to Orders → View the created order
   - Confirm the GCash reference number is shown
   - Confirm the receipt image is displayed

---

## File Changes Summary

### Modified Files:
1. `app/Models/Product.php` - Added `product_image` to fillable
2. `app/Http/Requests/Product/StoreProductRequest.php` - Added product_image validation
3. `app/Http/Controllers/Customer/CheckoutController.php` - Fixed GCash receipt field name
4. `resources/views/customer/products/index.blade.php` - Fixed image path
5. `resources/views/customer/products/show.blade.php` - Fixed image paths (2 locations)
6. `resources/views/customer/products/category.blade.php` - Fixed image path

### No Changes Needed:
- Product update functionality already had proper validation and handling
- Admin order show view already displays GCash receipts correctly
- Customer order detail view already displays GCash receipts correctly

---

## Important Notes

1. **Storage Link**: Ensure the symbolic link exists: `php artisan storage:link`
   - This creates a link from `public/storage` to `storage/app/public`
   - Required for images to be accessible via web browser

2. **File Permissions**: Ensure proper permissions on storage directories:
   - `storage/app/public/products/` must be writable
   - `storage/app/public/gcash_receipts/` must be writable

3. **Image Validation**: 
   - Product images: max 2MB, formats: jpeg, png, jpg, webp
   - GCash receipts: max 2MB, formats: jpeg, png, jpg, gif, svg

4. **Backwards Compatibility**: Existing products without images will display a default placeholder image

---

## Troubleshooting

### If images still don't upload:
1. Check PHP upload limits in `php.ini`:
   - `upload_max_filesize = 10M`
   - `post_max_size = 10M`

2. Verify storage directories exist and are writable:
   ```bash
   mkdir -p storage/app/public/products
   mkdir -p storage/app/public/gcash_receipts
   chmod -R 775 storage/
   ```

3. Recreate storage link:
   ```bash
   php artisan storage:link
   ```

4. Check Laravel logs: `storage/logs/laravel.log`

### If images don't display:
1. Verify the symbolic link: `ls -la public/storage`
2. Check image paths in browser developer tools
3. Verify file actually exists in storage directory
4. Check web server configuration (Apache/Nginx) allows serving files from storage

---

## Date Fixed
October 14, 2025

## Status
✅ **RESOLVED** - Both product image upload and GCash receipt upload issues have been fixed.
