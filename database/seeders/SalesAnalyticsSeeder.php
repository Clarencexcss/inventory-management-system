<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalesReport;
use App\Models\MonthlyExpense;

class SalesAnalyticsSeeder extends Seeder
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
                $grossSales = rand(90000, 120000);

                // Generate expenses
                $electricityBill = rand(8000, 10000);
                $staffSalaries = rand(20000, 25000);
                $productResupply = rand(15000, 20000);
                $equipmentMaintenance = rand(5000, 8000);

                // Create expense record
                $expense = MonthlyExpense::create([
                    'year' => $year,
                    'month' => $month,
                    'electricity_bill' => $electricityBill,
                    'staff_salaries' => $staffSalaries,
                    'product_resupply' => $productResupply,
                    'equipment_maintenance' => $equipmentMaintenance,
                    // Total will be auto-calculated by the model's boot method
                ]);

                // Calculate total expenses and net profit
                $totalExpenses = $expense->total;
                $netProfit = $grossSales - $totalExpenses;

                // Create sales report
                SalesReport::create([
                    'year' => $year,
                    'month' => $month,
                    'gross_sales' => $grossSales,
                    'total_expenses' => $totalExpenses,
                    'net_profit' => $netProfit,
                    'notes' => "Auto-generated sample data for {$this->getMonthName($month)} {$year}"
                ]);
            }
        }

        $this->command->info('Sales analytics data seeded successfully for 2020-2024!');
        $this->command->info('Total records created:');
        $this->command->info('- Sales Reports: ' . SalesReport::count());
        $this->command->info('- Monthly Expenses: ' . MonthlyExpense::count());
    }

    /**
     * Get month name from number
     */
    private function getMonthName($month)
    {
        return date('F', mktime(0, 0, 0, $month, 1));
    }
}
