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
        Schema::create('staff_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('month'); // Format: YYYY-MM
            $table->float('attendance_rate')->default(0); // percentage (0-100)
            $table->float('task_completion_rate')->default(0); // percentage (0-100)
            $table->float('customer_feedback_score')->default(0); // 1-5 scale
            $table->float('overall_performance')->nullable(); // auto-calculated percentage
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            // Ensure one entry per staff per month
            $table->unique(['staff_id', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_performance');
    }
};
