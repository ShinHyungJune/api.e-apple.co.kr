<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductPackage;
use Illuminate\Database\Seeder;

class ProductPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //DB::table('product_packages')->truncate();
        //DB::table('product_package_product')->truncate();
        $products = Product::get();
        ProductPackage::factory(30)
            ->create()->each(function ($package) use ($products) {
                // 패키지에 랜덤한 상품 3~5개 추가
                $package->products()->attach(
                    $products->random(rand(3, 5))->pluck('id')->toArray(),
                //['quantity' => rand(1, 10)] // 각 상품에 랜덤 수량 추가
                //['quantity' => 1]
                );
            });
    }
}
