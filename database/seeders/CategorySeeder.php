<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Phụ kiện ngoại vi',
                'slug' => 'peripherals',
                'description' => 'Chuột, bàn phím, webcam, tai nghe, micro và các thiết bị ngoại vi khác',
                'image' => '/Image/computer-peripherals.png',
            ],
            [
                'name' => 'Lưu trữ & kết nối',
                'slug' => 'storage',
                'description' => 'USB flash drive, SSD/HDD gắn ngoài, docking station, card reader',
                'image' => 'https://via.placeholder.com/300x200?text=Storage',
            ],
            [
                'name' => 'Nguồn & làm mát',
                'slug' => 'power',
                'description' => 'Bộ nguồn dự phòng, quạt tản nhiệt, pad tản nhiệt',
                'image' => 'https://via.placeholder.com/300x200?text=Power',
            ],
            [
                'name' => 'Bảo vệ & trang trí',
                'slug' => 'protection',
                'description' => 'Mousepad, miếng dán bảo vệ, giá đỡ laptop, vỏ case',
                'image' => 'https://via.placeholder.com/300x200?text=Protection',
            ],
            [
                'name' => 'Gaming',
                'slug' => 'gaming',
                'description' => 'Gamepad, ghế gaming, phụ kiện gaming chuyên dụng',
                'image' => 'https://via.placeholder.com/300x200?text=Gaming',
            ],
            [
                'name' => 'Bảo mật',
                'slug' => 'security',
                'description' => 'Khóa bảo mật laptop, USB security key',
                'image' => 'https://via.placeholder.com/300x200?text=Security',
            ],
            [
                'name' => 'Văn phòng',
                'slug' => 'office',
                'description' => 'Đế kê tài liệu, máy hủy giấy, bảng tương tác',
                'image' => 'https://via.placeholder.com/300x200?text=Office',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
