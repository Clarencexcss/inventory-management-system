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
        Schema::table('suppliers', function (Blueprint $table) {
            // Add analytics fields for supplier performance tracking
            if (!Schema::hasColumn('suppliers', 'contact_person')) {
                $table->string('contact_person')->nullable()->after('name');
            }
            if (!Schema::hasColumn('suppliers', 'delivery_rating')) {
                $table->decimal('delivery_rating', 3, 2)->default(0.00)->comment('Delivery performance rating (0-5 scale)')->after('status');
            }
            if (!Schema::hasColumn('suppliers', 'average_lead_time')) {
                $table->integer('average_lead_time')->default(0)->comment('Average delivery time in days')->after('delivery_rating');
            }
            if (!Schema::hasColumn('suppliers', 'total_procurements')) {
                $table->integer('total_procurements')->default(0)->comment('Total number of procurements')->after('average_lead_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Drop analytics fields in reverse order
            $table->dropColumn([
                'total_procurements',
                'average_lead_time',
                'delivery_rating',
                'contact_person'
            ]);
        });
    }
};
