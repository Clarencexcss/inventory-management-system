<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\Product;
use App\Models\Procurement;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SupplierAnalyticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds procurement records for the existing 2 suppliers only.
     */
    public function run(): void
    {
        // Get the 2 existing suppliers
        $suppliers = Supplier::take(2)->get();
        
        if ($suppliers->count() < 2) {
            $this->command->warn('Less than 2 suppliers found in database. Please ensure at least 2 suppliers exist.');
            return;
        }

        // Get random products for procurement
        $products = Product::take(10)->get();
        
        if ($products->isEmpty()) {
            $this->command->warn('No products found in database.');
            return;
        }

        $this->command->info('Seeding procurement records for ' . $suppliers->count() . ' suppliers...');

        // Generate procurement data for the last 12 months
        foreach ($suppliers as $supplier) {
            $this->command->info("Processing supplier: {$supplier->name}");
            
            foreach (range(1, 12) as $month) {
                // Create 2-4 procurement records per supplier per month
                $recordsPerMonth = rand(2, 4);
                
                for ($i = 0; $i < $recordsPerMonth; $i++) {
                    $product = $products->random();
                    $quantitySupplied = rand(50, 300);
                    $unitCost = rand(100, 500);
                    $totalCost = $quantitySupplied * $unitCost;
                    
                    // Random date within the month
                    $expectedDate = Carbon::now()
                        ->subMonths(12 - $month)
                        ->setDay(rand(1, 28));
                    
                    // 80% on-time delivery rate
                    $isOnTime = rand(1, 100) <= 80;
                    $deliveryDate = $isOnTime 
                        ? $expectedDate->copy()->subDays(rand(0, 2))
                        : $expectedDate->copy()->addDays(rand(1, 7));
                    
                    $status = $isOnTime ? 'on-time' : 'delayed';
                    
                    // Random defective rate (0-5%)
                    $defectiveRate = rand(0, 500) / 100; // 0.00 to 5.00
                    
                    Procurement::create([
                        'supplier_id' => $supplier->id,
                        'product_id' => $product->id,
                        'quantity_supplied' => $quantitySupplied,
                        'expected_delivery_date' => $expectedDate,
                        'delivery_date' => $deliveryDate,
                        'total_cost' => $totalCost,
                        'status' => $status,
                        'defective_rate' => $defectiveRate,
                    ]);
                }
            }
        }

        $totalRecords = Procurement::count();
        $this->command->info("✅ Successfully seeded {$totalRecords} procurement records!");
    }
}
