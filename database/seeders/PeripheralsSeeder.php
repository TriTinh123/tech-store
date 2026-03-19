<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PeripheralsSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key constraint checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Truncate existing products to avoid duplicate slug errors
        Product::query()->delete();
        
        // Re-enable foreign key constraint checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $peripheralsData = [
            // 🖥️ PERIPHERALS (spnv1–10)
            ['name' => 'Computer Mouse',                         'category' => 'peripherals', 'price' =>  34.99, 'image' => '/images/spnv1.jpg'],
            ['name' => 'RGB Keyboard',                           'category' => 'peripherals', 'price' =>  79.99, 'image' => '/images/spnv2.jpg'],
            ['name' => 'HD Webcam',                              'category' => 'peripherals', 'price' =>  49.99, 'image' => '/images/spnv3.jpg'],
            ['name' => 'Microphone / Headset with Mic',          'category' => 'peripherals', 'price' =>  59.99, 'image' => '/images/spnv4.jpg'],
            ['name' => 'Graphics Tablet',                        'category' => 'peripherals', 'price' =>  89.99, 'image' => '/images/spnv5.jpg'],
            ['name' => 'Computer Monitor',                       'category' => 'peripherals', 'price' => 299.99, 'image' => '/images/spnv6.jpg'],
            ['name' => 'Computer Speakers',                      'category' => 'peripherals', 'price' =>  44.99, 'image' => '/images/spnv7.jpg'],
            ['name' => 'Headphones / Headset',                   'category' => 'peripherals', 'price' =>  69.99, 'image' => '/images/spnv8.jpg'],
            ['name' => 'Printer',                                'category' => 'peripherals', 'price' => 199.99, 'image' => '/images/spnv9.jpg'],
            ['name' => 'Scanner',                                'category' => 'peripherals', 'price' => 149.99, 'image' => '/images/spnv10.jpg'],

            // 🧰 STORAGE & CONNECTIVITY (splt1–10)
            ['name' => 'USB Flash Drive',                        'category' => 'storage', 'price' =>  19.99, 'image' => '/images/splt1.jpg'],
            ['name' => 'External SSD Drive',                     'category' => 'storage', 'price' =>  89.99, 'image' => '/images/splt2.jpg'],
            ['name' => 'External HDD Drive',                     'category' => 'storage', 'price' =>  69.99, 'image' => '/images/splt3.jpg'],
            ['name' => 'Docking Station',                        'category' => 'storage', 'price' => 129.99, 'image' => '/images/splt4.jpg'],
            ['name' => 'Multi-Port USB Hub',                     'category' => 'storage', 'price' =>  39.99, 'image' => '/images/splt5.jpg'],
            ['name' => 'Card Reader',                            'category' => 'storage', 'price' =>  14.99, 'image' => '/images/splt6.jpg'],
            ['name' => 'USB Cable',                              'category' => 'storage', 'price' =>   9.99, 'image' => '/images/splt7.jpg'],
            ['name' => 'HDMI Cable',                             'category' => 'storage', 'price' =>  12.99, 'image' => '/images/splt8.jpg'],
            ['name' => 'DisplayPort Cable',                      'category' => 'storage', 'price' =>  15.99, 'image' => '/images/splt9.jpg'],
            ['name' => 'Ethernet Network Cable',                 'category' => 'storage', 'price' =>  11.99, 'image' => '/images/splt10.jpg'],

            // ⚡ POWER & COOLING (spd1–3)
            ['name' => 'UPS Battery Backup',                     'category' => 'power', 'price' => 179.99, 'image' => '/images/spd1.jpg'],
            ['name' => 'External Cooling Fan',                   'category' => 'power', 'price' =>  34.99, 'image' => '/images/spd2.jpg'],
            ['name' => 'Laptop Cooling Pad',                     'category' => 'power', 'price' =>  49.99, 'image' => '/images/spd3.jpg'],

            // 🪶 PROTECTION & DECORATION (spbv1–8)
            ['name' => 'Mouse Pad',                              'category' => 'protection', 'price' =>  19.99, 'image' => '/images/spbv1.jpg'],
            ['name' => 'Laptop Protective Film',                 'category' => 'protection', 'price' =>  14.99, 'image' => '/images/spbv2.jpg'],
            ['name' => 'Laptop Protective Sleeve',               'category' => 'protection', 'price' =>  24.99, 'image' => '/images/spbv3.jpg'],
            ['name' => 'Laptop Stand',                           'category' => 'protection', 'price' =>  34.99, 'image' => '/images/spbv4.jpg'],
            ['name' => 'Laptop Cooling Stand',                   'category' => 'protection', 'price' =>  44.99, 'image' => '/images/spbv5.jpg'],
            ['name' => 'PC Case',                                'category' => 'protection', 'price' => 139.99, 'image' => '/images/spbv6.jpg'],
            ['name' => 'PC Case Accessories',                    'category' => 'protection', 'price' =>  29.99, 'image' => '/images/spbv7.jpg'],
            ['name' => 'Cable Management Accessories',           'category' => 'protection', 'price' =>  19.99, 'image' => '/images/spbv8.jpg'],

            // 🎮 GAMING (spg1–6)
            ['name' => 'Gamepad',                               'category' => 'gaming', 'price' =>  69.99, 'image' => '/images/spg1.jpg'],
            ['name' => 'Joystick',                               'category' => 'gaming', 'price' =>  49.99, 'image' => '/images/spg2.jpg'],
            ['name' => 'Mouse Bungee',                           'category' => 'gaming', 'price' =>  34.99, 'image' => '/images/spg3.jpg'],
            ['name' => 'Professional Gaming Chair',              'category' => 'gaming', 'price' => 399.99, 'image' => '/images/spg4.jpg'],
            ['name' => 'Gaming Lumbar Support Pillow',           'category' => 'gaming', 'price' =>  44.99, 'image' => '/images/spg5.jpg'],
            ['name' => 'VR Controller',                          'category' => 'gaming', 'price' => 249.99, 'image' => '/images/spg6.jpg'],

            // 🔒 SECURITY (spbm1–4)
            ['name' => 'Laptop Security Lock',                   'category' => 'security', 'price' =>  24.99, 'image' => '/images/spbm1.jpg'],
            ['name' => 'Security Cable Lock',                    'category' => 'security', 'price' =>  19.99, 'image' => '/images/spbm2.jpg'],
            ['name' => 'USB Security Lock',                      'category' => 'security', 'price' =>  29.99, 'image' => '/images/spbm3.jpg'],
            ['name' => 'Hardware Security Token',                'category' => 'security', 'price' =>  49.99, 'image' => '/images/spbm4.jpg'],

            // 🖨️ OFFICE (spvp1–4)
            ['name' => 'Document Holder',                        'category' => 'office', 'price' =>  24.99, 'image' => '/images/spvp1.jpg'],
            ['name' => 'Copy Stand',                             'category' => 'office', 'price' =>  34.99, 'image' => '/images/spvp2.jpg'],
            ['name' => 'Document Shredder',                      'category' => 'office', 'price' => 199.99, 'image' => '/images/spvp3.jpg'],
            ['name' => 'Interactive Whiteboard',                 'category' => 'office', 'price' => 599.99, 'image' => '/images/spvp4.jpg'],
        ];

        foreach ($peripheralsData as $product) {
            $category = Category::where('slug', $product['category'])->first();

            Product::create([
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'description' => 'High-quality computer accessory',
                'category_id' => $category?->id,
                'price' => $product['price'],
                'original_price' => $product['price'] * 1.15,
                'stock' => rand(10, 100),
                'image' => $product['image'],
                'is_featured' => rand(0, 1),
                'rating' => rand(4, 5),
                'reviews_count' => rand(10, 500),
            ]);
        }
    }
}
