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
        Schema::create('monthly_sales_inputs', function (Blueprint $table) {
            $table->id();
            $table->integer('year')->default(2025);
            $table->integer('month');
            $table->decimal('sales_amount', 12, 2);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('monthly_sales_inputs');
    }
};
