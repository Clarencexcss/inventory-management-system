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
        Schema::create('monthly_expenses', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->integer('month');
            $table->decimal('electricity_bill', 10, 2)->default(0);
            $table->decimal('staff_salaries', 10, 2)->default(0);
            $table->decimal('product_resupply', 10, 2)->default(0);
            $table->decimal('equipment_maintenance', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
            
            // Unique constraint for year-month combination
            $table->unique(['year', 'month']);
            
            // Indexes for performance
            $table->index('year');
            $table->index(['year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_expenses');
    }
};
