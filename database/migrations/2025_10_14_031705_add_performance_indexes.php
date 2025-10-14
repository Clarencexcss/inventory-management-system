<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes for products table
        Schema::table('products', function (Blueprint $table) {
            // Index for quantity filtering (low stock queries)
            $table->index('quantity');
            
            // Index for expiration date filtering
            $table->index('expiration_date');
            
            // Composite index for low stock + expiration queries
            $table->index(['quantity', 'expiration_date']);
            
            // Index for category_id (frequently used in joins)
            $table->index('category_id');
            
            // Index for unit_id (frequently used in joins)
            $table->index('unit_id');
            
            // Index for supplier_id (if exists)
            if (Schema::hasColumn('products', 'supplier_id')) {
                $table->index('supplier_id');
            }
        });

        // Add indexes for orders table
        Schema::table('orders', function (Blueprint $table) {
            // Index for order_status filtering (most important for analytics)
            $table->index('order_status');
            
            // Index for order_date filtering (trends and date ranges)
            $table->index('order_date');
            
            // Composite index for status + date queries
            $table->index(['order_status', 'order_date']);
            
            // Index for customer_id (foreign key)
            $table->index('customer_id');
            
            // Index for payment_type (analytics grouping)
            $table->index('payment_type');
            
            // Index for cancelled_by (staff performance)
            if (Schema::hasColumn('orders', 'cancelled_by')) {
                $table->index('cancelled_by');
            }
        });

        // Add indexes for order_details table
        Schema::table('order_details', function (Blueprint $table) {
            // Index for order_id (foreign key)
            $table->index('order_id');
            
            // Index for product_id (foreign key)
            $table->index('product_id');
            
            // Composite index for order + product queries
            $table->index(['order_id', 'product_id']);
        });

        // Add indexes for purchases table
        Schema::table('purchases', function (Blueprint $table) {
            // Index for date filtering
            $table->index('date');
            
            // Index for status filtering
            $table->index('status');
            
            // Index for supplier_id (foreign key)
            $table->index('supplier_id');
            
            // Composite index for supplier + date queries
            $table->index(['supplier_id', 'date']);
            
            // Composite index for status + date queries
            $table->index(['status', 'date']);
        });

        // Add indexes for users table (staff performance)
        Schema::table('users', function (Blueprint $table) {
            // Index for role filtering
            $table->index('role');
            
            // Index for status filtering
            $table->index('status');
            
            // Composite index for role + status queries
            $table->index(['role', 'status']);
        });

        // Add indexes for suppliers table
        Schema::table('suppliers', function (Blueprint $table) {
            // Index for status filtering
            if (Schema::hasColumn('suppliers', 'status')) {
                $table->index('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['quantity']);
            $table->dropIndex(['expiration_date']);
            $table->dropIndex(['quantity', 'expiration_date']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['unit_id']);
            
            if (Schema::hasColumn('products', 'supplier_id')) {
                $table->dropIndex(['supplier_id']);
            }
        });

        // Remove indexes from orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['order_status']);
            $table->dropIndex(['order_date']);
            $table->dropIndex(['order_status', 'order_date']);
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['payment_type']);
            
            if (Schema::hasColumn('orders', 'cancelled_by')) {
                $table->dropIndex(['cancelled_by']);
            }
        });

        // Remove indexes from order_details table
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['product_id']);
            $table->dropIndex(['order_id', 'product_id']);
        });

        // Remove indexes from purchases table
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['supplier_id']);
            $table->dropIndex(['supplier_id', 'date']);
            $table->dropIndex(['status', 'date']);
        });

        // Remove indexes from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['status']);
            $table->dropIndex(['role', 'status']);
        });

        // Remove indexes from suppliers table
        Schema::table('suppliers', function (Blueprint $table) {
            if (Schema::hasColumn('suppliers', 'status')) {
                $table->dropIndex(['status']);
            }
        });
    }
};