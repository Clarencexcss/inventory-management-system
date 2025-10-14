<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class UpdateSupplierAnalyticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Updates analytics fields for all existing suppliers based on their procurement data.
     */
    public function run(): void
    {
        $this->command->info('Updating supplier analytics fields...');

        $suppliers = Supplier::all();

        if ($suppliers->isEmpty()) {
            $this->command->warn('No suppliers found in database.');
            return;
        }

        foreach ($suppliers as $supplier) {
            $this->command->info("Processing supplier: {$supplier->name}");

            // Update contact person if empty
            if (empty($supplier->contact_person)) {
                $supplier->contact_person = $this->generateContactName($supplier->name);
            }

            // Update analytics based on procurements
            $supplier->updateAnalytics();

            $this->command->info("  ✓ Total Procurements: {$supplier->total_procurements}");
            $this->command->info("  ✓ Average Lead Time: {$supplier->average_lead_time} days");
            $this->command->info("  ✓ Delivery Rating: {$supplier->delivery_rating}/5.00");
        }

        $this->command->info("✅ Successfully updated analytics for {$suppliers->count()} suppliers!");
    }

    /**
     * Generate a contact person name based on supplier name
     */
    private function generateContactName(string $supplierName): string
    {
        $firstNames = ['Juan', 'Maria', 'Jose', 'Ana', 'Carlos', 'Rosa', 'Miguel', 'Elena'];
        $lastNames = ['Santos', 'Reyes', 'Cruz', 'Garcia', 'Mendoza', 'Torres', 'Flores', 'Ramos'];

        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];

        return "{$firstName} {$lastName}";
    }
}
