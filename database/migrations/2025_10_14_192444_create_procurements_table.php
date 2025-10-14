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
        Schema::create('procurements', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Supplier::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Product::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->integer('quantity_supplied');
            $table->date('expected_delivery_date');
            $table->date('delivery_date')->nullable();
            $table->decimal('total_cost', 10, 2);
            $table->string('status')->default('on-time')->comment('on-time or delayed');
            $table->decimal('defective_rate', 5, 2)->default(0.00)->comment('Percentage of defective items');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('supplier_id');
            $table->index('product_id');
            $table->index('status');
            $table->index('delivery_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurements');
    }
};
