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
        //DB::table('products')->truncate();
        $products = [
            ['name' => '국내산 거창군 딸기, 싱그러운 향과 맛', 'price' => 12000, 'category_ids' => [3], 'option' => '1kg, 1팩',
                'categories' => ['suggestion', 'best', 'gift', 'sale', 'popular', 'juicy'],
                'images' => [
                    asset('/images/samples/1/1.png'),
                    asset('/images/samples/1/2.jpg'),
                ],
            ],
            ['name' => '국내산 거창군 마늘, 아삭한 신선함', 'price' => 8000, 'category_ids' => [3], 'option' => '1kg',
                'categories' => ['suggestion', 'best', 'gift', 'sale', 'popular', 'juicy'],
                'images' => [
                    asset('/images/samples/2/1.jpg'),
                    asset('/images/samples/2/2.jpg'),
                    asset('/images/samples/2/3.jpg'),
                ],
            ],
            ['name' => '국내산 거창군 사과', 'price' => 15000, 'category_ids' => [3], 'option' => '3kg, 1팩',
                'categories' => ['suggestion', 'best', 'gift', 'sale', 'popular', 'juicy'],
                'images' => [
                    asset('/images/samples/3/9Z5A3529.JPG'),
                    asset('/images/samples/3/9Z5A3555.JPG'),
                    asset('/images/samples/3/9Z5A3584.JPG'),
                    asset('/images/samples/3/9Z5A3590.JPG'),
                ],
            ],
            ['name' => '국내산 거창군 샤인머스켓', 'price' => 22000, 'category_ids' => [3], 'option' => '1kg, 1팩',
                'categories' => ['suggestion', 'best', 'gift', 'sale', 'popular', 'juicy'],
                'images' => [
                    asset('/images/samples/4/1.jpg'),
                    asset('/images/samples/4/2.jpg'),
                    asset('/images/samples/4/3.jpg'),
                    asset('/images/samples/4/4.jpg'),
                    asset('/images/samples/4/5.jpg'),
                ],
            ],
            ['name' => '국내산 고춧가루, 깊고 진한 풍미', 'price' => 14000, 'category_ids' => [4], 'option' => '500g, 1팩',
                'categories' => ['suggestion', 'best', 'gift', 'sale', 'popular', 'juicy'],
                'images' => [
                    asset('/images/samples/5/1.jpg'),
                    asset('/images/samples/5/2.jpg'),
                    asset('/images/samples/5/3.jpg'),
                ],
            ],
            ['name' => '국내산 복숭아', 'price' => 16000, 'category_ids' => [4], 'option' => '2kg, 1팩',
                'categories' => ['suggestion', 'best', 'gift', 'sale', 'popular', 'juicy'],
                'images' => [
                    asset('/images/samples/6/1.jpg'),
                    asset('/images/samples/6/2.jpg'),
                ],
            ],
            ['name' => '국내산 블루베리', 'price' => 7000, 'category_ids' => [4], 'option' => '500g, 1팩',
                'categories' => ['suggestion', 'best', 'gift', 'sale', 'popular', 'juicy'],
                'images' => [
                    asset('/images/samples/7/1.jpg'),
                ],
            ],
            ['name' => '국내산 신선한 감자, 부드러운 맛', 'price' => 9000, 'category_ids' => [4], 'option' => '3kg, 1팩',
                'categories' => ['suggestion', 'best', 'gift', 'sale', 'popular', 'juicy'],
                'images' => [
                    asset('/images/samples/8/1.jpg'),
                ],
            ],
            ['name' => '국내산 오렌지', 'price' => 10000, 'category_ids' => [5], 'option' => '2kg, 1팩',
                'categories' => ['suggestion', 'best', 'gift', 'sale', 'popular', 'juicy'],
                'images' => [
                    asset('/images/samples/9/1.jpg'),
                ],
            ],
            ['name' => '국내산 자두, 싱그러운 향과 맛', 'price' => 9000, 'category_ids' => [5], 'option' => '1kg, 1팩',
                'categories' => ['suggestion', 'best', 'gift', 'sale', 'popular', 'juicy'],
                'images' => [
                    asset('/images/samples/10/1.jpg'),
                    asset('/images/samples/10/2.jpg'),
                ],
            ],
            ['name' => '국내산 참외', 'price' => 12000, 'category_ids' => [5], 'option' => '2kg, 1팩',
                'categories' => ['suggestion', 'best', 'gift', 'sale', 'popular', 'juicy'],
                'images' => [
                    asset('/images/samples/11/1.jpg'),
                ],
            ],
            ['name' => '국내산 햇감귤, 달콤한 제주의 맛', 'price' => 7500, 'category_ids' => [5], 'option' => '1kg, 1팩',
                'categories' => ['suggestion', 'best', 'gift', 'sale', 'popular', 'juicy'],
                'images' => [
                    asset('/images/samples/12/1.jpg'),
                    asset('/images/samples/12/2.jpg'),
                    asset('/images/samples/12/3.jpg'),
                ],
            ],
            ['name' => '망고, 열대과일의 달콤함', 'price' => 18000, 'category_ids' => [7], 'option' => '2kg',
                'categories' => ['suggestion', 'best', 'gift', 'sale', 'popular', 'juicy'],
                'images' => [
                    asset('/images/samples/13/1.jpg'),
                    asset('/images/samples/13/2.jpg'),
                    asset('/images/samples/13/3.jpg'),
                    asset('/images/samples/13/4.jpg'),
                ],
            ],
            ['name' => '멜론, 촉촉하고 달콤한 과일', 'price' => 10000, 'category_ids' => [7], 'option' => '1.5kg',
                'categories' => ['suggestion', 'best', 'gift', 'sale', 'popular', 'juicy'],
                'images' => [
                    asset('/images/samples/14/1.jpg'),
                    asset('/images/samples/14/2.jpg'),
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
