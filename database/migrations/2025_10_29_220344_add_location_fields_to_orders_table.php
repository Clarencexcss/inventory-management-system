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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('city', 100)->default('Cabuyao')->after('delivery_address');
            $table->string('postal_code', 10)->default('4025')->after('city');
            $table->string('barangay', 100)->nullable()->after('postal_code');
            $table->string('street_name', 255)->nullable()->after('barangay');
            $table->string('building', 100)->nullable()->after('street_name');
            $table->string('house_no', 50)->nullable()->after('building');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['city', 'postal_code', 'barangay', 'street_name', 'building', 'house_no']);
        });
    }
};