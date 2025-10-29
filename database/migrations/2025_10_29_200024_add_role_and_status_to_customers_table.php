<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Check if columns exist before adding them
            if (!Schema::hasColumn('customers', 'role')) {
                $table->enum('role', ['admin', 'staff', 'customer'])
                    ->default('customer')
                    ->after('email');
            }
                
            if (!Schema::hasColumn('customers', 'status')) {
                $table->enum('status', ['active', 'inactive', 'suspended'])
                    ->default('active')
                    ->after('role');
            }
                
            if (!Schema::hasColumn('customers', 'deleted_at')) {
                $table->softDeletes(); // For account deactivation
            }
        });
        
        // Set existing customers to have default values if columns were added
        if (Schema::hasColumn('customers', 'role') || Schema::hasColumn('customers', 'status')) {
            DB::table('customers')->update([
                'role' => 'customer',
                'status' => 'active'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('customers', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('customers', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};