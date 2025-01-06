<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class TestProductDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $products = [
            ['name' => '국내산 거창군 딸기, 싱그러운 향과 맛', 'price' => 12000, 'category_ids' => [3], 'option' => '1kg, 1팩',
                'images' => [
                    asset('/images/samples/1/열매나무 딸기_썸네일.jpg'),
                    asset('/images/samples/1/딸기1.png'),
                ],
            ],
            ['name' => '국내산 거창군 마늘, 아삭한 신선함', 'price' => 8000, 'category_ids' => [3], 'option' => '1kg',
                'images' => [
                    asset('/images/samples/2/[열매나무]-무안-마늘_썸네일.jpg'),
                    asset('/images/samples/2/[열매나무]-무안-마늘_썸네일1.jpg'),
                    asset('/images/samples/2/[열매나무]-무안-마늘_썸네일2.jpg'),
                ],
            ],
            ['name' => '국내산 거창군 사과', 'price' => 15000, 'category_ids' => [3], 'option' => '3kg, 1팩',
                'images' => [
                    asset('/images/samples/3/9Z5A3529.JPG'),
                    asset('/images/samples/3/9Z5A3555.JPG'),
                    asset('/images/samples/3/9Z5A3584.JPG'),
                    asset('/images/samples/3/9Z5A3590.JPG'),
                ],
            ],
            ['name' => '국내산 거창군 샤인머스켓', 'price' => 22000, 'category_ids' => [3], 'option' => '1kg, 1팩',
                'images' => [
                    asset('/images/samples/4/아름아리 샤인머스켓 1.8kg_썸네일.jpg'),
                    asset('/images/samples/4/샤인머스켓_thumb.jpg'),
                    asset('/images/samples/4/[열매나무]-샤인머스켓_썸네일.jpg'),
                    asset('/images/samples/4/[열매나무]-샤인머스켓_썸네일_2.jpg'),
                    asset('/images/samples/4/[열매나무]-샤인머스켓_썸네일_1.jpg'),
                ],
            ],
            ['name' => '국내산 고춧가루, 깊고 진한 풍미', 'price' => 14000, 'category_ids' => [4], 'option' => '500g, 1팩',
                'images' => [
                    asset('/images/samples/5/고춧가루_썸네일_1.jpg'),
                    asset('/images/samples/5/고춧가루_썸네일_2.jpg'),
                    asset('/images/samples/5/고춧가루_썸네일_3.jpg'),
                ],
            ],
            ['name' => '국내산 복숭아', 'price' => 16000, 'category_ids' => [4], 'option' => '2kg, 1팩',
                'images' => [
                    asset('/images/samples/6/[열매나무]-백도복숭아_썸네일2.jpg'),
                    asset('/images/samples/6/[열매나무]-백도복숭아_썸네일.jpg'),
                ],
            ],
            ['name' => '국내산 블루베리', 'price' => 7000, 'category_ids' => [4], 'option' => '500g, 1팩',
                'images' => [
                    asset('/images/samples/7/블루베리 썸네일.jpg'),
                ],
            ],
            ['name' => '국내산 신선한 감자, 부드러운 맛', 'price' => 9000, 'category_ids' => [4], 'option' => '3kg, 1팩',
                'images' => [
                    asset('/images/samples/8/수미감자_썸네일.jpg'),
                ],
            ],
            ['name' => '국내산 오렌지', 'price' => 10000, 'category_ids' => [5], 'option' => '2kg, 1팩',
                'images' => [
                    asset('/images/samples/9/열매나무_네이블-고당도-오렌지_썸네일.jpg'),
                ],
            ],
            ['name' => '국내산 자두, 싱그러운 향과 맛', 'price' => 9000, 'category_ids' => [5], 'option' => '1kg, 1팩',
                'images' => [
                    asset('/images/samples/10/[아름아리] 대석자두_썸네일.jpg'),
                    asset('/images/samples/10/[열매나무] 대석자두_썸네일.jpg'),
                ],
            ],
            ['name' => '국내산 참외', 'price' => 12000, 'category_ids' => [5], 'option' => '2kg, 1팩',
                'images' => [
                    asset('/images/samples/11/열매나무_스테비아 참외_썸네일.jpg'),
                ],
            ],
            ['name' => '국내산 햇감귤, 달콤한 제주의 맛', 'price' => 7500, 'category_ids' => [5], 'option' => '1kg, 1팩',
                'images' => [
                    asset('/images/samples/12/KakaoTalk_20241015_155202113_01.jpg'),
                    asset('/images/samples/12/KakaoTalk_20241015_155202113.jpg'),
                    asset('/images/samples/12/썸네일_수정본.jpg'),
                ],
            ],
            ['name' => '망고, 열대과일의 달콤함', 'price' => 18000, 'category_ids' => [7], 'option' => '2kg',
                'images' => [
                    asset('/images/samples/13/열매나무_항공직송_태국망고_썸네일.jpg'),
                    asset('/images/samples/13/KakaoTalk_20240307_133042738_01.jpg'),
                    asset('/images/samples/13/KakaoTalk_20240307_133042738_02.jpg'),
                    asset('/images/samples/13/KakaoTalk_20240307_155327791.jpg'),
                ],
            ],
            ['name' => '멜론, 촉촉하고 달콤한 과일', 'price' => 10000, 'category_ids' => [7], 'option' => '1.5kg',
                'images' => [
                    asset('/images/samples/14/[아름아리] 하미과멜론_3kg_썸네일.jpg'),
                    asset('/images/samples/14/열매나무_파파야멜론_썸네일.jpg'),
                ],
            ],
        ];

        foreach ($products as $product) {
            $option = $product['option'];
            $images = $product['images'];
            unset($product['option']);
            unset($product['images']);

            $original_price = $product['price'] * rand(1, 1.3);
            $product = [...$product, 'original_price' => $original_price, 'stock_quantity' => 1000];

            $product = Product::create($product);
            foreach ($images as $image) {
                $encodedUrl = dirname($image) . '/' . rawurlencode(basename($image));
                $product->addMediaFromUrl($encodedUrl)->toMediaCollection(Product::IMAGES);
            }
            $product->options()->create([
                    'name' => $option, 'price' => $product['price'],
                    'original_price' => $original_price, 'stock_quantity' => 1000]
            );
        }
    }
}
