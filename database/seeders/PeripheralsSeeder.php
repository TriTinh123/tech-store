<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PeripheralsSeeder extends Seeder
{
    public function run(): void
    {
        $peripheralsData = [
            // PHỤ KIỆN NGOẠI VI (Peripherals)
            ['name' => 'Logitech MX Master 3S', 'category' => 'peripherals', 'price' => 99.99, 'image' => '/images/logitech-MX master-3S.jpg'],
            ['name' => 'Corsair K95 Platinum XT Mechanical Keyboard', 'category' => 'peripherals', 'price' => 199.99, 'image' => '/images/CorsairK95.jpg'],
            ['name' => 'Razer DeathAdder V3 Gaming Mouse', 'category' => 'peripherals', 'price' => 69.99, 'image' => '/images/razer.jpg'],
            ['name' => 'SteelSeries Arctis 9 Wireless Headset', 'category' => 'peripherals', 'price' => 329.99, 'image' => '/images/steelseries.jpg'],
            ['name' => 'Logitech C920 Pro HD Webcam', 'category' => 'peripherals', 'price' => 79.99, 'image' => 'https://via.placeholder.com/300x300?text=Logitech+C920'],

            // LƯU TRỮ & KẾT NỐI (Storage)
            ['name' => 'Samsung 870 QVO 1TB SSD', 'category' => 'storage', 'price' => 79.99, 'image' => 'https://via.placeholder.com/300x300?text=Samsung+SSD+870'],
            ['name' => 'WD My Passport 2TB External HDD', 'category' => 'storage', 'price' => 59.99, 'image' => 'https://via.placeholder.com/300x300?text=WD+Passport+2TB'],
            ['name' => 'Seagate Barracuda 4TB External Drive', 'category' => 'storage', 'price' => 89.99, 'image' => 'https://via.placeholder.com/300x300?text=Seagate+Barracuda'],
            ['name' => 'Anker USB-C Hub 7-in-1', 'category' => 'storage', 'price' => 39.99, 'image' => 'https://via.placeholder.com/300x300?text=Anker+USB+Hub'],
            ['name' => 'SanDisk Extreme 64GB USB 3.1', 'category' => 'storage', 'price' => 19.99, 'image' => 'https://via.placeholder.com/300x300?text=SanDisk+USB'],

            // NGUỒN & LÀM MÁT (Power)
            ['name' => 'APC Back-UPS Pro 1500VA', 'category' => 'power', 'price' => 179.99, 'image' => 'https://via.placeholder.com/300x300?text=APC+UPS'],
            ['name' => 'Corsair RM750e 750W Gold Power Supply', 'category' => 'power', 'price' => 89.99, 'image' => 'https://via.placeholder.com/300x300?text=Corsair+PSU+750W'],
            ['name' => 'Noctua NH-D15 CPU Cooler', 'category' => 'power', 'price' => 99.99, 'image' => 'https://via.placeholder.com/300x300?text=Noctua+Cooler'],
            ['name' => 'Be Quiet! Dark Rock Pro 4 Cooler', 'category' => 'power', 'price' => 89.99, 'image' => 'https://via.placeholder.com/300x300?text=Be+Quiet+Cooler'],
            ['name' => 'ASUS TUF Gaming Cooler Pad', 'category' => 'power', 'price' => 49.99, 'image' => 'https://via.placeholder.com/300x300?text=ASUS+Cooler+Pad'],

            // BẢO VỆ & TRANG TRÍ (Protection)
            ['name' => 'SteelSeries QcK Heavy Mousepad', 'category' => 'protection', 'price' => 44.99, 'image' => 'https://via.placeholder.com/300x300?text=SteelSeries+Mousepad'],
            ['name' => 'Razer Basilisk Ultimate Dock + Mousepad', 'category' => 'protection', 'price' => 99.99, 'image' => 'https://via.placeholder.com/300x300?text=Razer+Dock'],
            ['name' => 'Lamicall Laptop Stand Aluminum', 'category' => 'protection', 'price' => 34.99, 'image' => 'https://via.placeholder.com/300x300?text=Lamicall+Stand'],
            ['name' => 'Corsair Vengeance RGB PRO Case', 'category' => 'protection', 'price' => 139.99, 'image' => 'https://via.placeholder.com/300x300?text=Corsair+Case'],
            ['name' => 'Cable Matters USB-A Cable 2-Pack', 'category' => 'protection', 'price' => 14.99, 'image' => 'https://via.placeholder.com/300x300?text=Cable+Matters'],

            // GAMING
            ['name' => 'Xbox Series X Wireless Controller', 'category' => 'gaming', 'price' => 69.99, 'image' => 'https://via.placeholder.com/300x300?text=Xbox+Controller'],
            ['name' => 'Sony PlayStation 5 DualSense Controller', 'category' => 'gaming', 'price' => 74.99, 'image' => 'https://via.placeholder.com/300x300?text=PS5+Controller'],
            ['name' => 'Secretlab Omega 2022 Gaming Chair', 'category' => 'gaming', 'price' => 399.99, 'image' => 'https://via.placeholder.com/300x300?text=Secretlab+Chair'],
            ['name' => 'Herman Miller x Logitech Gaming Chair', 'category' => 'gaming', 'price' => 1495.00, 'image' => 'https://via.placeholder.com/300x300?text=Herman+Miller+Chair'],
            ['name' => 'Razer Mouse Bungee v3', 'category' => 'gaming', 'price' => 34.99, 'image' => 'https://via.placeholder.com/300x300?text=Razer+Bungee'],

            // BẢO MẬT (Security)
            ['name' => 'Kensington Laptop Lock Cable', 'category' => 'security', 'price' => 24.99, 'image' => 'https://via.placeholder.com/300x300?text=Kensington+Lock'],
            ['name' => 'YubiKey 5 Series USB Security Key', 'category' => 'security', 'price' => 45.00, 'image' => 'https://via.placeholder.com/300x300?text=YubiKey+5'],
            ['name' => 'Kingston IronKey 32GB Encrypted USB', 'category' => 'security', 'price' => 54.99, 'image' => 'https://via.placeholder.com/300x300?text=Kingston+IronKey'],
            ['name' => 'Fellowes MFP Hard Drive Security Lock', 'category' => 'security', 'price' => 34.99, 'image' => 'https://via.placeholder.com/300x300?text=Fellowes+Lock'],
            ['name' => 'Targus Defcon Cable Lock', 'category' => 'security', 'price' => 29.99, 'image' => 'https://via.placeholder.com/300x300?text=Targus+Lock'],

            // VĂN PHÒNG (Office)
            ['name' => '3M Document Holder Stand', 'category' => 'office', 'price' => 29.99, 'image' => 'https://via.placeholder.com/300x300?text=3M+Document+Holder'],
            ['name' => 'Fellowes Powershred 79Ci Cross-Cut Paper Shredder', 'category' => 'office', 'price' => 199.99, 'image' => 'https://via.placeholder.com/300x300?text=Fellowes+Shredder'],
            ['name' => 'Smart Board Pro 86" Interactive Display', 'category' => 'office', 'price' => 5999.99, 'image' => 'https://via.placeholder.com/300x300?text=SmartBoard+Pro'],
            ['name' => 'Elago Desktop Organizer', 'category' => 'office', 'price' => 19.99, 'image' => 'https://via.placeholder.com/300x300?text=Elago+Organizer'],
            ['name' => 'AmazonBasics Adjustable Monitor Stand Riser', 'category' => 'office', 'price' => 34.99, 'image' => 'https://via.placeholder.com/300x300?text=Monitor+Stand'],
        ];

        foreach ($peripheralsData as $product) {
            $category = Category::where('slug', $product['category'])->first();

            Product::create([
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'description' => 'Sản phẩm phụ kiện máy tính chất lượng cao',
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
