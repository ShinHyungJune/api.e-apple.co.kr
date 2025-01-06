<?php

namespace Database\Seeders;

use App\Models\Sweetness;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SweetnessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sweetnesses')->truncate();
        //Sweetness::factory()->count(10)->create();
        $sweetnesses = [
            ['fruit_name' => '홍로사과', 'sweetness' => rand(10, 20), 'standard_sweetness' => rand(10, 20), 'is_display' => true,
                'image' => asset('/images/samples/3/9Z5A3529.JPG'),
            ],
            ['fruit_name' => '감귤', 'sweetness' => rand(10, 20), 'standard_sweetness' => rand(10, 20), 'is_display' => true,
                'image' => asset('/images/samples/12/1.jpg'),
            ],
            ['fruit_name' => '샤인머스켓', 'sweetness' => rand(10, 20), 'standard_sweetness' => rand(10, 20), 'is_display' => true,
                'image' => asset('/images/samples/4/1.jpg'),
            ],
            ['fruit_name' => '멜론', 'sweetness' => rand(10, 20), 'standard_sweetness' => rand(10, 20), 'is_display' => true,
                'image' => asset('/images/samples/14/1.jpg'),
            ],
            ['fruit_name' => '블루베리', 'sweetness' => rand(10, 20), 'standard_sweetness' => rand(10, 20), 'is_display' => true,
                'image' => asset('/images/samples/7/1.jpg'),
            ],
        ];

        foreach ($sweetnesses as $sweetness) {
            $image = $sweetness['image'];
            unset($sweetness['image']);
            $sweetness = Sweetness::create($sweetness);
            $sweetness->addMediaFromUrl($image)->toMediaCollection(Sweetness::IMAGES);
        }

    }
}
