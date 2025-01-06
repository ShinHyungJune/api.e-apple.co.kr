<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('banners')->truncate();
        //Banner::factory(5)->create();
        $banners = [
            [
                'title' => "신선함 그대로! 🍓 오늘 아침 수확한 과일을 바로 집으로!",
                'description' => '우리 과일 쇼핑몰은 매일 아침, 신선하게 수확된 과일만을 제공합니다. 입안 가득 퍼지는 자연의 맛을 직접 경험해 보세요. 신선한 과일을 바로 집으로 배송해 드리며, 고객님께 최상의 품질을 약속드립니다. 집에서 편하게 프리미엄 과일을 즐기세요!',
                'is_active' => true,
                'image' => asset('/images/samples/pexels-pixabay-161559.jpg'),
            ],
            [
                'title' => "달콤한 행복, 한 입의 즐거움 🍎 특별 할인 이벤트 진행 중!",
                'description' => '지금, 달콤하고 신선한 과일을 할인된 가격으로 만나보세요! 건강한 간식, 디저트, 또는 요리에 제격인 다양한 과일을 준비했습니다. 특별한 혜택과 함께 더욱 풍성한 쇼핑을 즐기세요. 한정된 시간 동안만 제공되는 기회를 놓치지 마세요!',
                'is_active' => true,
                'image' => asset('/images/samples/pexels-mali-102104.jpg'),

            ],
            [
                'title' => "자연의 맛을 담다 🍇 프리미엄 과일, 지금 만나보세요!",
                'description' => '자연 그대로의 맛을 자랑하는 프리미엄 과일을 소개합니다. 철저한 품질 관리 아래, 고른 최고의 과일만을 선별하여 제공하며, 고객님의 입맛을 만족시킬 수 있도록 최선을 다하고 있습니다. 언제 어디서나 신선한 과일을 간편하게 즐기세요!',
                'is_active' => true,
                'image' => asset('/images/samples/pexels-markusspiske-1343537.jpg')
            ]
        ];

        foreach ($banners as $banner) {
            $image = $banner['image'];
            unset($banner['image']);
            $banner = Banner::create($banner);
            $banner->addMediaFromUrl($image)->toMediaCollection(Banner::IMAGES);
        }

    }
}
