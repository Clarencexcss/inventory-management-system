<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\Product;
use App\Models\Procurement;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EnhancedSupplierAnalyticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds realistic procurement records with proper date distribution for analytics charts.
     */
    public function run(): void
    {
        // Get the 2 existing suppliers
        $suppliers = Supplier::all();
        
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

        $this->command->info('Seeding enhanced procurement records for ' . $suppliers->count() . ' suppliers...');
        $this->command->info('Date range: Last 12 months with proper monthly distribution');

        $totalRecordsCreated = 0;

        // Generate procurement data for the last 12 months
        foreach ($suppliers as $supplier) {
            $this->command->info("Processing supplier: {$supplier->name}");
            
            // Start from 11 months ago to current month (12 months total)
            for ($monthsAgo = 11; $monthsAgo >= 0; $monthsAgo--) {
                // Create 3-5 procurement records per supplier per month
                $recordsPerMonth = rand(3, 5);
                
                for ($i = 0; $i < $recordsPerMonth; $i++) {
                    $product = $products->random();
                    $quantitySupplied = rand(50, 300);
                    $unitCost = rand(100, 500);
                    $totalCost = $quantitySupplied * $unitCost;
                    
                    // Create a date within this specific month
                    $baseDate = Carbon::now()->subMonths($monthsAgo);
                    $dayOfMonth = rand(1, min(28, $baseDate->daysInMonth));
                    $expectedDate = $baseDate->copy()->setDay($dayOfMonth);
                    
                    // 80% on-time delivery rate
                    $isOnTime = rand(1, 100) <= 80;
                    
                    if ($isOnTime) {
                        // On-time: deliver on expected date or 1-2 days early
                        $deliveryDate = $expectedDate->copy()->subDays(rand(0, 2));
                    } else {
                        // Delayed: deliver 1-7 days late
                        $deliveryDate = $expectedDate->copy()->addDays(rand(1, 7));
                    }
                    
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
                    
                    $totalRecordsCreated++;
                }
                
                $monthName = $baseDate->format('M Y');
                $this->command->info("  ✓ Created {$recordsPerMonth} records for {$monthName}");
            }
        }

        $this->command->info("✅ Successfully seeded {$totalRecordsCreated} procurement records!");
        
        // Display date range for verification
        $oldestDate = Procurement::orderBy('delivery_date', 'asc')->first()->delivery_date;
        $newestDate = Procurement::orderBy('delivery_date', 'desc')->first()->delivery_date;
        $this->command->info("📅 Date range: {$oldestDate->format('M Y')} to {$newestDate->format('M Y')}");
    }
}
