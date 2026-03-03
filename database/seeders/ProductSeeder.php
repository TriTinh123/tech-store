<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Disable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Xóa tất cả sản phẩm cũ
        Product::truncate();

        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $products = [
            // LAPTOPS
            [
                'name' => 'Apple MacBook Pro 16" M3 Max',
                'slug' => Str::slug('Apple MacBook Pro 16 M3 Max'),
                'description' => 'Laptop chuyên nghiệp với chip M3 Max, màn hình Retina 16 inch',
                'category' => 'Laptop',
                'price' => 3499.99,
                'original_price' => 3999.99,
                'stock' => 15,
                'image' => 'https://via.placeholder.com/300x300?text=MacBook+Pro+16',
                'manufacturer' => 'Apple',
                'specifications' => ['cpu' => 'M3 Max', 'ram' => '36GB', 'storage' => '1TB SSD', 'display' => '16" Retina'],
                'is_featured' => true,
                'rating' => 5,
                'reviews_count' => 342,
            ],
            [
                'name' => 'Dell XPS 15 Plus',
                'slug' => Str::slug('Dell XPS 15 Plus'),
                'description' => 'Laptop gaming mạnh mẽ với RTX 4080, Intel i9 gen 14',
                'category' => 'Laptop',
                'price' => 2799.99,
                'original_price' => 3299.99,
                'stock' => 20,
                'image' => 'https://via.placeholder.com/300x300?text=Dell+XPS+15',
                'manufacturer' => 'Dell',
                'specifications' => ['cpu' => 'Intel i9-14900HX', 'gpu' => 'RTX 4080', 'ram' => '32GB', 'display' => '15.6" 4K'],
                'is_featured' => true,
                'rating' => 5,
                'reviews_count' => 256,
            ],
            [
                'name' => 'ASUS ROG Zephyrus G16',
                'slug' => Str::slug('ASUS ROG Zephyrus G16'),
                'description' => 'Laptop gaming cao cấp với RTX 4090, tản nhiệt hiệu quả',
                'category' => 'Laptop',
                'price' => 3299.99,
                'original_price' => 3799.99,
                'stock' => 12,
                'image' => 'https://via.placeholder.com/300x300?text=ASUS+ROG+G16',
                'manufacturer' => 'ASUS',
                'specifications' => ['cpu' => 'Intel i9-14900HX', 'gpu' => 'RTX 4090', 'ram' => '32GB', 'display' => '16" 240Hz'],
                'is_featured' => true,
                'rating' => 5,
                'reviews_count' => 198,
            ],
            [
                'name' => 'Lenovo ThinkPad X1 Carbon Gen 12',
                'slug' => Str::slug('Lenovo ThinkPad X1 Carbon Gen 12'),
                'description' => 'Laptop doanh nhân siêu nhẹ, bàn phím tuyệt vời',
                'category' => 'Laptop',
                'price' => 1599.99,
                'original_price' => 1899.99,
                'stock' => 25,
                'image' => 'https://via.placeholder.com/300x300?text=ThinkPad+X1',
                'manufacturer' => 'Lenovo',
                'specifications' => ['cpu' => 'Intel i7-1365U', 'ram' => '16GB', 'storage' => '512GB SSD', 'display' => '14" FHD'],
                'is_featured' => false,
                'rating' => 5,
                'reviews_count' => 412,
            ],
            [
                'name' => 'HP Spectre x360 16',
                'slug' => Str::slug('HP Spectre x360 16'),
                'description' => 'Laptop 2-in-1 chuyên dụng với màn hình OLED',
                'category' => 'Laptop',
                'price' => 2199.99,
                'original_price' => 2599.99,
                'stock' => 18,
                'image' => 'https://via.placeholder.com/300x300?text=HP+Spectre+x360',
                'manufacturer' => 'HP',
                'specifications' => ['cpu' => 'Intel i7-14700H', 'ram' => '32GB', 'storage' => '1TB SSD', 'display' => '16" OLED'],
                'is_featured' => false,
                'rating' => 5,
                'reviews_count' => 267,
            ],
            [
                'name' => 'Acer Swift 3 SF315-52G',
                'slug' => Str::slug('Acer Swift 3 SF315-52G'),
                'description' => 'Laptop mỏng nhẹ với hiệu suất tốt, giá cả phải chăng',
                'category' => 'Laptop',
                'price' => 999.99,
                'original_price' => 1299.99,
                'stock' => 30,
                'image' => 'https://via.placeholder.com/300x300?text=Acer+Swift+3',
                'manufacturer' => 'Acer',
                'specifications' => ['cpu' => 'AMD Ryzen 7 5700U', 'ram' => '16GB', 'storage' => '512GB SSD', 'display' => '15.6" FHD'],
                'is_featured' => false,
                'rating' => 4,
                'reviews_count' => 189,
            ],
            [
                'name' => 'MSI Raider GE67 HX',
                'slug' => Str::slug('MSI Raider GE67 HX'),
                'description' => 'Laptop gaming tầm trung với RTX 4060, hiệu suất cân bằng',
                'category' => 'Laptop',
                'price' => 1799.99,
                'original_price' => 2099.99,
                'stock' => 22,
                'image' => 'https://via.placeholder.com/300x300?text=MSI+Raider',
                'manufacturer' => 'MSI',
                'specifications' => ['cpu' => 'Intel i7-13700H', 'gpu' => 'RTX 4060', 'ram' => '16GB', 'display' => '15.6" 144Hz'],
                'is_featured' => false,
                'rating' => 5,
                'reviews_count' => 234,
            ],

            // ĐIỆN THOẠI
            [
                'name' => 'iPhone 15 Pro Max',
                'slug' => Str::slug('iPhone 15 Pro Max'),
                'description' => 'Smartphone flagship Apple với chip A17 Pro, camera 48MP',
                'category' => 'Điện thoại',
                'price' => 1299.99,
                'original_price' => 1499.99,
                'stock' => 40,
                'image' => 'https://via.placeholder.com/300x300?text=iPhone+15+Pro+Max',
                'manufacturer' => 'Apple',
                'specifications' => ['cpu' => 'A17 Pro', 'ram' => '8GB', 'storage' => '256GB', 'display' => '6.7" Super Retina'],
                'is_featured' => true,
                'rating' => 5,
                'reviews_count' => 678,
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'slug' => Str::slug('Samsung Galaxy S24 Ultra'),
                'description' => 'Smartphone Android mạnh nhất với Snapdragon 8 Gen 3',
                'category' => 'Điện thoại',
                'price' => 1299.99,
                'original_price' => 1499.99,
                'stock' => 35,
                'image' => 'https://via.placeholder.com/300x300?text=Galaxy+S24+Ultra',
                'manufacturer' => 'Samsung',
                'specifications' => ['cpu' => 'Snapdragon 8 Gen 3', 'ram' => '12GB', 'storage' => '512GB', 'display' => '6.8" AMOLED'],
                'is_featured' => true,
                'rating' => 5,
                'reviews_count' => 512,
            ],
            [
                'name' => 'Google Pixel 8 Pro',
                'slug' => Str::slug('Google Pixel 8 Pro'),
                'description' => 'Smartphone AI tuyệt vời với Tensor G3, camera xuất sắc',
                'category' => 'Điện thoại',
                'price' => 999.99,
                'original_price' => 1199.99,
                'stock' => 28,
                'image' => 'https://via.placeholder.com/300x300?text=Pixel+8+Pro',
                'manufacturer' => 'Google',
                'specifications' => ['cpu' => 'Tensor G3', 'ram' => '12GB', 'storage' => '256GB', 'display' => '6.7" OLED'],
                'is_featured' => true,
                'rating' => 5,
                'reviews_count' => 456,
            ],
            [
                'name' => 'OnePlus 12',
                'slug' => Str::slug('OnePlus 12'),
                'description' => 'Smartphone hiệu suất cao với sạc nhanh 100W',
                'category' => 'Điện thoại',
                'price' => 799.99,
                'original_price' => 999.99,
                'stock' => 32,
                'image' => 'https://via.placeholder.com/300x300?text=OnePlus+12',
                'manufacturer' => 'OnePlus',
                'specifications' => ['cpu' => 'Snapdragon 8 Gen 3', 'ram' => '12GB', 'storage' => '256GB', 'display' => '6.7" AMOLED'],
                'is_featured' => false,
                'rating' => 5,
                'reviews_count' => 345,
            ],
            [
                'name' => 'Xiaomi 14 Ultra',
                'slug' => Str::slug('Xiaomi 14 Ultra'),
                'description' => 'Smartphone camera giá tốt với Snapdragon 8 Gen 3 Leading',
                'category' => 'Điện thoại',
                'price' => 699.99,
                'original_price' => 899.99,
                'stock' => 45,
                'image' => 'https://via.placeholder.com/300x300?text=Xiaomi+14+Ultra',
                'manufacturer' => 'Xiaomi',
                'specifications' => ['cpu' => 'Snapdragon 8 Gen 3', 'ram' => '12GB', 'storage' => '512GB', 'display' => '6.73" AMOLED'],
                'is_featured' => false,
                'rating' => 5,
                'reviews_count' => 287,
            ],
            [
                'name' => 'Samsung Galaxy A54 5G',
                'slug' => Str::slug('Samsung Galaxy A54 5G'),
                'description' => 'Smartphone tầm trung tốt với pin bền, giá hợp lý',
                'category' => 'Điện thoại',
                'price' => 499.99,
                'original_price' => 699.99,
                'stock' => 55,
                'image' => 'https://via.placeholder.com/300x300?text=Galaxy+A54',
                'manufacturer' => 'Samsung',
                'specifications' => ['cpu' => 'Exynos 1380', 'ram' => '8GB', 'storage' => '256GB', 'display' => '6.4" AMOLED'],
                'is_featured' => false,
                'rating' => 4,
                'reviews_count' => 223,
            ],
            [
                'name' => 'iPhone 15',
                'slug' => Str::slug('iPhone 15'),
                'description' => 'iPhone tiêu chuẩn mới với chip A16 Bionic, cổng USB-C',
                'category' => 'Điện thoại',
                'price' => 799.99,
                'original_price' => 999.99,
                'stock' => 50,
                'image' => 'https://via.placeholder.com/300x300?text=iPhone+15',
                'manufacturer' => 'Apple',
                'specifications' => ['cpu' => 'A16 Bionic', 'ram' => '6GB', 'storage' => '128GB', 'display' => '6.1" Super Retina'],
                'is_featured' => false,
                'rating' => 5,
                'reviews_count' => 534,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
