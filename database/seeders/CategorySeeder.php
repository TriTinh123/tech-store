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
                'name' => 'Peripherals',
                'slug' => 'peripherals',
                'description' => 'Mice, keyboards, webcams, headsets, microphones and other peripheral devices',
                'image' => '/Image/peripherals/mouse.jpg',
            ],
            [
                'name' => 'Storage & Connectivity',
                'slug' => 'storage',
                'description' => 'USB flash drives, external SSD/HDD, docking stations, card readers',
                'image' => '/Image/storage/kingston-usb.jpg',
            ],
            [
                'name' => 'Power & Cooling',
                'slug' => 'power',
                'description' => 'Power banks, cooling fans, cooling pads',
                'image' => '/Image/power/corsair-power-supply.jpg',
            ],
            [
                'name' => 'Protection & Decoration',
                'slug' => 'protection',
                'description' => 'Mousepads, laptop protective films, laptop stands, PC cases',
                'image' => '/Image/protection/steelseries-qck-mousepad.jpg',
            ],
            [
                'name' => 'Gaming',
                'slug' => 'gaming',
                'description' => 'Gamepads, gaming chairs, dedicated gaming accessories',
                'image' => '/Image/gaming/razer-gaming-gear.jpg',
            ],
            [
                'name' => 'Security',
                'slug' => 'security',
                'description' => 'Laptop security locks, cable locks, USB locks, hardware security tokens',
                'image' => '/images/spbm1.jpg',
            ],
            [
                'name' => 'Office',
                'slug' => 'office',
                'description' => 'Document holders, paper shredders, interactive whiteboards',
                'image' => '/Image/office/fellowes-laminator.jpg',
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
