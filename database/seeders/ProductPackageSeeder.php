<?php

namespace Database\Seeders;

use App\Enums\ProductPackageType;
use App\Models\ProductPackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement("SET foreign_key_checks=0");
        DB::table('product_packages')->truncate();
        DB::table('product_package_product')->truncate();
        DB::statement("SET foreign_key_checks=1");
        /*$products = Product::get();
        ProductPackage::factory(30)
            ->create()->each(function ($package) use ($products) {
                // 패키지에 랜덤한 상품 3~5개 추가
                $package->products()->attach(
                    $products->random(rand(3, 5))->pluck('id')->toArray(),
                //['quantity' => rand(1, 10)] // 각 상품에 랜덤 수량 추가
                //['quantity' => 1]
                );
            });*/
        $productPackages = [
            [
                'title' => "달콤한 맛의 정석 🍑 국산 복숭아",
                'description' => "거창군의 맑은 공기와 깨끗한 물로 재배된 국산 복숭아는 달콤함과 부드러움이 어우러진 최상의 맛을 자랑합니다. 수확 후 바로 선별해 신선함을 그대로 전달하며, 가족과 함께 건강하고 맛있는 간식을 즐길 수 있는 최고의 선택입니다.",
                'type' => ProductPackageType::MONTHLY_SUGGESTION,
                'category_title' => '국산',
                'product_ids' => [6],
                'image' => asset('/images/samples/6/1.jpg'),
            ],
            /*[
                'title' => "이국적인 달콤함 🍍 신선한 수입 파인애플",
                'description' => '태양 아래 자란 수입 파인애플은 풍부한 과즙과 달콤한 향기로 남녀노소 모두의 입맛을 사로잡습니다. 동남아시아의 비옥한 토양에서 재배된 최고급 품질로, 샐러드, 디저트, 주스 등 다양하게 활용 가능합니다. 집에서도 간편하게 열대과일의 매력을 느껴보세요!',
                'type' => ProductPackageType::MONTHLY_SUGGESTION,
                'category_title' => '수입',
                'product_ids' => [],
                'image' => asset('/images/samples/6/1.jpg'),
            ],*/
            [
                'title' => "지금 맛봐야 할 과일 🍓 제철 딸기",
                'description' => '겨울철 가장 달콤하고 상큼한 제철 딸기! 한입 베어 물면 퍼지는 싱그러운 향과 풍부한 과즙이 일상의 활력을 더해줍니다. 신선하고 건강한 간식으로 딸기를 선택하세요. 지금 딱 먹기 좋은 맛과 품질로 준비했습니다.',
                'type' => ProductPackageType::MONTHLY_SUGGESTION,
                'category_title' => '제철',
                'product_ids' => [1],
                'image' => asset('/images/samples/1/1.png'),
            ],
            [
                'title' => "언제 어디서나 간편하게 🥭 망고 건조 과일",
                'description' => '열대과일의 대표 주자 망고를 간편하게 즐길 수 있도록 건조 과일로 만들었습니다. 본연의 달콤한 풍미는 그대로 유지하면서, 가볍게 휴대하여 어디서나 즐길 수 있습니다. 건강한 간식이나 요거트 토핑으로도 완벽한 선택입니다.',
                'type' => ProductPackageType::MONTHLY_SUGGESTION,
                'category_title' => '가공품',
                'product_ids' => [13],
                'image' => asset('/images/samples/13/1.jpg'),
            ],
            [
                'title' => "넉넉한 양으로 즐기는 🍇 대용량 샤인머스캣",
                'description' => '달콤하고 향긋한 샤인머스캣을 대용량으로 만나보세요! 과일을 사랑하는 가족, 파티, 또는 대규모 모임에 제격인 넉넉한 양으로 준비했습니다. 알이 크고 당도가 높은 품질 좋은 샤인머스캣으로 소중한 순간을 더욱 특별하게 만들어 보세요.',
                'type' => ProductPackageType::MONTHLY_SUGGESTION,
                'category_title' => '대용량',
                'product_ids' => [4],
                'image' => asset('/images/samples/4/1.jpg'),
            ],
            [
                'title' => "혼자서도 충분히 🍊 소용량 제주 감귤",
                'description' => '제주도에서 자란 감귤을 1~2인 가구를 위해 적당한 양으로 준비했습니다. 상큼한 맛과 건강함을 간편하게 즐길 수 있어, 바쁜 일상 속에서 간식으로 딱! 신선함과 맛을 놓치지 않은 제주 감귤로 힐링하세요.',
                'type' => ProductPackageType::MONTHLY_SUGGESTION,
                'category_title' => '소용량',
                'product_ids' => [12],
                'image' => asset('/images/samples/12/1.jpg'),
            ],



            //////////////// MD 추천 ////////////////
            [
                'title' => "건강과 행복을 선물하세요 🍎 국내산 제철 과일 혼합 세트",
                'description' => '제철 과일인 사과, 배, 감귤 등 신선한 국내산 과일을 엄선하여 구성한 건강한 선물세트입니다. 다양한 과일로 풍성함을 더했으며, 받는 분의 건강과 행복을 기원하는 마음을 담았습니다. 특별한 분께 자연의 선물을 전달해보세요.',
                'type' => ProductPackageType::MD_SUGGESTION,
                'product_ids' => [1, 12],
                'image' => asset('/images/samples/12/1.jpg'),
            ],
            [
                'title' => "감사의 마음을 담아 🍇 프리미엄 샤인머스캣 선물세트",
                'description' => '고급 과일의 대명사, 샤인머스캣을 선물세트로 준비했습니다. 알이 굵고 당도가 높은 샤인머스캣은 고급스러운 패키지에 담아 소중한 분들에게 감사의 마음을 전하기에 완벽한 선택입니다. 특별한 날, 잊지 못할 달콤함을 선물하세요.',
                'type' => ProductPackageType::MD_SUGGESTION,
                'product_ids' => [4],
                'image' => asset('/images/samples/4/1.jpg'),
            ],
            /*[
                'title' => "언제나 환영받는 선물 🎁 고급 견과 & 건조 과일 세트",
                'description' => '프리미엄 견과류와 자연 그대로의 건조 과일을 담은 선물세트로 건강과 맛을 동시에 챙겨보세요. 간편하게 즐길 수 있어 남녀노소 누구에게나 사랑받는 선물입니다. 세련된 포장으로 품격까지 더해 특별한 순간을 더욱 빛내드립니다.',
                'type' => ProductPackageType::MD_SUGGESTION,
                'product_ids' => [],
                'image' => asset('/images/samples/12/1.jpg'),
            ]*/
        ];

        foreach ($productPackages as $productPackage) {
            $image = $productPackage['image'];
            $productIds = $productPackage['product_ids'];
            unset($productPackage['image']);
            unset($productPackage['product_ids']);

            $productPackage = ProductPackage::create($productPackage);
            $productPackage->addMediaFromUrl($image)->toMediaCollection(ProductPackage::IMAGES);
            $productPackage->products()->attach($productIds);
        }

    }
}
