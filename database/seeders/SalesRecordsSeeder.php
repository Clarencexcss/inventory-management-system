<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalesRecord;

class SalesRecordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate sample data for years 2020-2024
        $years = [2020, 2021, 2022, 2023, 2024];

        foreach ($years as $year) {
            // Generate data for all 12 months
            for ($month = 1; $month <= 12; $month++) {
                // Generate random sales between ₱90,000 - ₱120,000
                $totalSales = rand(90000, 120000);

                // Generate expenses with updated ranges
                $electricity = rand(8000, 10000);
                $staffSalaries = rand(20000, 35000);
                $productRestock = rand(30000, 45000);
                $equipmentPurchases = rand(5000, 15000);

                // Calculate total expenses and net profit
                $totalExpenses = $electricity + $staffSalaries + $productRestock + $equipmentPurchases;
                $netProfit = $totalSales - $totalExpenses;

                // Create sales record
                SalesRecord::create([
                    'year' => $year,
                    'month' => $month,
                    'total_sales' => $totalSales,
                    'total_expenses' => $totalExpenses,
                    'net_profit' => $netProfit
                ]);
            }
        }

        $this->command->info('Sales records seeded successfully for 2020-2024!');
        $this->command->info('Total records created: ' . SalesRecord::count());
    }
}
