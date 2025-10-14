<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\MeatCut;
use App\Models\User;
use App\Models\ProductUpdateLog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Get all admin/staff users for random assignment
        $users = User::whereIn('role', ['admin', 'staff'])->pluck('id')->toArray();
        
        if (empty($users)) {
            $this->command->warn('No admin/staff users found. Creating default admin user.');
            $defaultUser = User::create([
                'name' => 'Admin User',
                'username' => 'admin',
                'email' => 'admin@butcherpro.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now()
            ]);
            $users = [$defaultUser->id];
        }

        // Get categories, units, and meat cuts
        $kgUnit = Unit::firstOrCreate(['name' => 'Kilogram'], ['slug' => 'kg', 'short_code' => 'kg']);
        $meatCategory = Category::firstOrCreate(['name' => 'Meat'], ['slug' => 'meat']);
        
        // Get or create meat cuts
        $meatCuts = [
            ['name' => 'Ribeye', 'animal_type' => 'beef', 'cut_type' => 'Premium Steak', 'default_price_per_kg' => 450, 'minimum_stock_level' => 10],
            ['name' => 'Sirloin', 'animal_type' => 'beef', 'cut_type' => 'Prime Cut', 'default_price_per_kg' => 420, 'minimum_stock_level' => 10],
            ['name' => 'Tenderloin', 'animal_type' => 'beef', 'cut_type' => 'Premium Steak', 'default_price_per_kg' => 550, 'minimum_stock_level' => 8],
            ['name' => 'T-Bone', 'animal_type' => 'beef', 'cut_type' => 'Steak', 'default_price_per_kg' => 480, 'minimum_stock_level' => 10],
            ['name' => 'Brisket', 'animal_type' => 'beef', 'cut_type' => 'Roast', 'default_price_per_kg' => 380, 'minimum_stock_level' => 15],
            ['name' => 'Pork Chop', 'animal_type' => 'pork', 'cut_type' => 'Chop', 'default_price_per_kg' => 280, 'minimum_stock_level' => 20],
            ['name' => 'Pork Belly', 'animal_type' => 'pork', 'cut_type' => 'Belly', 'default_price_per_kg' => 320, 'minimum_stock_level' => 25],
            ['name' => 'Pork Ribs', 'animal_type' => 'pork', 'cut_type' => 'Ribs', 'default_price_per_kg' => 350, 'minimum_stock_level' => 15],
            ['name' => 'Chicken Breast', 'animal_type' => 'chicken', 'cut_type' => 'Breast', 'default_price_per_kg' => 180, 'minimum_stock_level' => 30],
            ['name' => 'Chicken Thigh', 'animal_type' => 'chicken', 'cut_type' => 'Thigh', 'default_price_per_kg' => 160, 'minimum_stock_level' => 35],
            ['name' => 'Chicken Wings', 'animal_type' => 'chicken', 'cut_type' => 'Wings', 'default_price_per_kg' => 220, 'minimum_stock_level' => 25],
            ['name' => 'Lamb Chops', 'animal_type' => 'lamb', 'cut_type' => 'Chop', 'default_price_per_kg' => 650, 'minimum_stock_level' => 8],
            ['name' => 'Lamb Shank', 'animal_type' => 'lamb', 'cut_type' => 'Shank', 'default_price_per_kg' => 580, 'minimum_stock_level' => 10],
        ];

        $cutIds = [];
        foreach ($meatCuts as $cut) {
            $meatCut = MeatCut::firstOrCreate(
                ['name' => $cut['name'], 'animal_type' => $cut['animal_type']],
                [
                    'cut_type' => $cut['cut_type'],
                    'default_price_per_kg' => $cut['default_price_per_kg'],
                    'minimum_stock_level' => $cut['minimum_stock_level']
                ]
            );
            $cutIds[$cut['name']] = $meatCut->id;
        }

        // Create 20 sample products
        $products = [
            // Beef Products
            [
                'name' => 'Premium Beef Ribeye',
                'slug' => 'premium-beef-ribeye',
                'code' => 'BF-RIB-001',
                'meat_cut_id' => $cutIds['Ribeye'],
                'quantity' => rand(30, 100),
                'price_per_kg' => 450,
                'selling_price' => 470,
                'buying_price' => 400,
                'quantity_alert' => 10,
            ],
            [
                'name' => 'Angus Beef Sirloin',
                'slug' => 'angus-beef-sirloin',
                'code' => 'BF-SIR-001',
                'meat_cut_id' => $cutIds['Sirloin'],
                'quantity' => rand(25, 80),
                'price_per_kg' => 420,
                'selling_price' => 440,
                'buying_price' => 370,
                'quantity_alert' => 10,
            ],
            [
                'name' => 'Premium Tenderloin',
                'slug' => 'premium-tenderloin',
                'code' => 'BF-TEN-001',
                'meat_cut_id' => $cutIds['Tenderloin'],
                'quantity' => rand(15, 50),
                'price_per_kg' => 550,
                'selling_price' => 580,
                'buying_price' => 500,
                'quantity_alert' => 8,
            ],
            [
                'name' => 'T-Bone Steak',
                'slug' => 't-bone-steak',
                'code' => 'BF-TBN-001',
                'meat_cut_id' => $cutIds['T-Bone'],
                'quantity' => rand(20, 60),
                'price_per_kg' => 480,
                'selling_price' => 500,
                'buying_price' => 430,
                'quantity_alert' => 10,
            ],
            [
                'name' => 'Beef Brisket',
                'slug' => 'beef-brisket',
                'code' => 'BF-BRS-001',
                'meat_cut_id' => $cutIds['Brisket'],
                'quantity' => rand(40, 100),
                'price_per_kg' => 380,
                'selling_price' => 400,
                'buying_price' => 340,
                'quantity_alert' => 15,
            ],
            // Pork Products
            [
                'name' => 'Premium Pork Chop',
                'slug' => 'premium-pork-chop',
                'code' => 'PK-CHP-001',
                'meat_cut_id' => $cutIds['Pork Chop'],
                'quantity' => rand(50, 120),
                'price_per_kg' => 280,
                'selling_price' => 300,
                'buying_price' => 250,
                'quantity_alert' => 20,
            ],
            [
                'name' => 'Pork Belly Slice',
                'slug' => 'pork-belly-slice',
                'code' => 'PK-BEL-001',
                'meat_cut_id' => $cutIds['Pork Belly'],
                'quantity' => rand(60, 150),
                'price_per_kg' => 320,
                'selling_price' => 340,
                'buying_price' => 280,
                'quantity_alert' => 25,
            ],
            [
                'name' => 'Baby Back Ribs',
                'slug' => 'baby-back-ribs',
                'code' => 'PK-RIB-001',
                'meat_cut_id' => $cutIds['Pork Ribs'],
                'quantity' => rand(30, 80),
                'price_per_kg' => 350,
                'selling_price' => 370,
                'buying_price' => 310,
                'quantity_alert' => 15,
            ],
            [
                'name' => 'Smoked Pork Belly',
                'slug' => 'smoked-pork-belly',
                'code' => 'PK-BEL-002',
                'meat_cut_id' => $cutIds['Pork Belly'],
                'quantity' => rand(25, 70),
                'price_per_kg' => 340,
                'selling_price' => 360,
                'buying_price' => 300,
                'quantity_alert' => 12,
            ],
            // Chicken Products
            [
                'name' => 'Fresh Chicken Breast',
                'slug' => 'fresh-chicken-breast',
                'code' => 'CK-BRS-001',
                'meat_cut_id' => $cutIds['Chicken Breast'],
                'quantity' => rand(80, 200),
                'price_per_kg' => 180,
                'selling_price' => 200,
                'buying_price' => 150,
                'quantity_alert' => 30,
            ],
            [
                'name' => 'Chicken Thigh Fillet',
                'slug' => 'chicken-thigh-fillet',
                'code' => 'CK-THI-001',
                'meat_cut_id' => $cutIds['Chicken Thigh'],
                'quantity' => rand(90, 220),
                'price_per_kg' => 160,
                'selling_price' => 180,
                'buying_price' => 135,
                'quantity_alert' => 35,
            ],
            [
                'name' => 'Chicken Wings',
                'slug' => 'chicken-wings',
                'code' => 'CK-WNG-001',
                'meat_cut_id' => $cutIds['Chicken Wings'],
                'quantity' => rand(70, 180),
                'price_per_kg' => 220,
                'selling_price' => 240,
                'buying_price' => 190,
                'quantity_alert' => 25,
            ],
            [
                'name' => 'Organic Chicken Breast',
                'slug' => 'organic-chicken-breast',
                'code' => 'CK-BRS-002',
                'meat_cut_id' => $cutIds['Chicken Breast'],
                'quantity' => rand(40, 100),
                'price_per_kg' => 220,
                'selling_price' => 240,
                'buying_price' => 180,
                'quantity_alert' => 20,
            ],
            [
                'name' => 'BBQ Chicken Wings',
                'slug' => 'bbq-chicken-wings',
                'code' => 'CK-WNG-002',
                'meat_cut_id' => $cutIds['Chicken Wings'],
                'quantity' => rand(50, 120),
                'price_per_kg' => 240,
                'selling_price' => 260,
                'buying_price' => 200,
                'quantity_alert' => 20,
            ],
            // Lamb Products
            [
                'name' => 'Premium Lamb Chops',
                'slug' => 'premium-lamb-chops',
                'code' => 'LB-CHP-001',
                'meat_cut_id' => $cutIds['Lamb Chops'],
                'quantity' => rand(15, 40),
                'price_per_kg' => 650,
                'selling_price' => 680,
                'buying_price' => 580,
                'quantity_alert' => 8,
            ],
            [
                'name' => 'Lamb Shank',
                'slug' => 'lamb-shank',
                'code' => 'LB-SHK-001',
                'meat_cut_id' => $cutIds['Lamb Shank'],
                'quantity' => rand(20, 50),
                'price_per_kg' => 580,
                'selling_price' => 610,
                'buying_price' => 520,
                'quantity_alert' => 10,
            ],
            // Additional variety
            [
                'name' => 'Wagyu Beef Ribeye',
                'slug' => 'wagyu-beef-ribeye',
                'code' => 'BF-RIB-002',
                'meat_cut_id' => $cutIds['Ribeye'],
                'quantity' => rand(10, 30),
                'price_per_kg' => 850,
                'selling_price' => 900,
                'buying_price' => 750,
                'quantity_alert' => 5,
            ],
            [
                'name' => 'Grass-Fed Sirloin',
                'slug' => 'grass-fed-sirloin',
                'code' => 'BF-SIR-002',
                'meat_cut_id' => $cutIds['Sirloin'],
                'quantity' => rand(25, 70),
                'price_per_kg' => 480,
                'selling_price' => 510,
                'buying_price' => 420,
                'quantity_alert' => 10,
            ],
            [
                'name' => 'Pork Loin Chops',
                'slug' => 'pork-loin-chops',
                'code' => 'PK-CHP-002',
                'meat_cut_id' => $cutIds['Pork Chop'],
                'quantity' => rand(45, 110),
                'price_per_kg' => 300,
                'selling_price' => 320,
                'buying_price' => 260,
                'quantity_alert' => 18,
            ],
            [
                'name' => 'Marinated Chicken Thighs',
                'slug' => 'marinated-chicken-thighs',
                'code' => 'CK-THI-002',
                'meat_cut_id' => $cutIds['Chicken Thigh'],
                'quantity' => rand(60, 140),
                'price_per_kg' => 190,
                'selling_price' => 210,
                'buying_price' => 155,
                'quantity_alert' => 25,
            ],
        ];

        foreach ($products as $productData) {
            // Create the product
            $product = Product::create([
                'name' => $productData['name'],
                'slug' => $productData['slug'],
                'code' => $productData['code'],
                'category_id' => $meatCategory->id,
                'unit_id' => $kgUnit->id,
                'meat_cut_id' => $productData['meat_cut_id'],
                'quantity' => $productData['quantity'],
                'price_per_kg' => $productData['price_per_kg'],
                'selling_price' => $productData['selling_price'],
                'buying_price' => $productData['buying_price'],
                'quantity_alert' => $productData['quantity_alert'],
                'updated_by' => $users[array_rand($users)],  // Random staff assignment
            ]);

            // Create initial update log
            ProductUpdateLog::create([
                'product_id' => $product->id,
                'staff_id' => null,  // Initial creation has no staff_id
                'user_id' => $product->updated_by,
                'action' => 'created',
                'changes' => json_encode([
                    'initial_quantity' => $product->quantity,
                    'initial_price' => $product->price_per_kg
                ]),
            ]);
        }

        $this->command->info('Created 20 meat products with staff tracking!');
    }
}
