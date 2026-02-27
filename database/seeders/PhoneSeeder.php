<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PhoneSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::firstOrCreate(
            ['name' => 'Phones'],
            ['description' => 'Smartphones and mobile devices']
        );

        $phones = [
            [
                'name' => 'iPhone 16 Pro Max 256GB - Titanium Black',
                'description' => 'Latest flagship iPhone with A18 Pro chip, 6.9-inch display, and advanced camera system.',
                'sku' => 'IP16-PM-256-BLK',
                'cost_price' => 185000.00,
                'selling_price' => 225000.00,
                'quantity_in_stock' => 5,
                'reorder_level' => 2,
            ],
            [
                'name' => 'iPhone 16 Pro 128GB - Natural Titanium',
                'description' => 'Pro features in a more compact size. A18 Pro chip and ProMotion display.',
                'sku' => 'IP16-P-128-NAT',
                'cost_price' => 160000.00,
                'selling_price' => 195000.00,
                'quantity_in_stock' => 8,
                'reorder_level' => 3,
            ],
            [
                'name' => 'iPhone 16 128GB - Ultramarine',
                'description' => 'The powerful standard iPhone 16 with new camera controls.',
                'sku' => 'IP16-128-BLU',
                'cost_price' => 115000.00,
                'selling_price' => 145000.00,
                'quantity_in_stock' => 12,
                'reorder_level' => 5,
            ],
            [
                'name' => 'iPhone 15 Pro Max 256GB - Blue Titanium',
                'description' => 'Previous generation flagship, still incredibly powerful with A17 Pro chip.',
                'sku' => 'IP15-PM-256-BLU',
                'cost_price' => 150000.00,
                'selling_price' => 178000.00,
                'quantity_in_stock' => 4,
                'reorder_level' => 2,
            ],
            [
                'name' => 'iPhone 15 128GB - Pink',
                'description' => 'Features Dynamic Island and 48MP Main camera.',
                'sku' => 'IP15-128-PNK',
                'cost_price' => 95000.00,
                'selling_price' => 118000.00,
                'quantity_in_stock' => 10,
                'reorder_level' => 4,
            ],
            [
                'name' => 'iPhone 14 128GB - Midnight',
                'description' => 'Reliable performance with dual-camera system and all-day battery life.',
                'sku' => 'IP14-128-BLK',
                'cost_price' => 82000.00,
                'selling_price' => 98000.00,
                'quantity_in_stock' => 15,
                'reorder_level' => 5,
            ],
            [
                'name' => 'iPhone 13 128GB - Starlight',
                'description' => 'Advanced dual-camera system and lightning-fast chip.',
                'sku' => 'IP13-128-WHT',
                'cost_price' => 68000.00,
                'selling_price' => 84000.00,
                'quantity_in_stock' => 20,
                'reorder_level' => 5,
            ],
        ];

        foreach ($phones as $phoneData) {
            $phoneData['category_id'] = $category->id;
            
            // Generate a fake IMEI for each item in stock if needed, 
            // but here we just assign one to the product record for testing.
            // In a real system, you'd track individual IMEIs in a separate table or serialized field.
            $phoneData['imei'] = '35' . mt_rand(1000000000000, 9999999999999);
            $phoneData['barcode'] = '19' . mt_rand(1000000000, 9999999999);
            $phoneData['total_cost'] = $phoneData['cost_price'] * $phoneData['quantity_in_stock'];

            Product::updateOrCreate(
                ['sku' => $phoneData['sku']],
                $phoneData
            );
        }

        $this->command->info('iPhone mock data seeded successfully!');
    }
}
