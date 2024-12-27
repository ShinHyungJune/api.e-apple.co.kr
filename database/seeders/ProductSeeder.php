<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory()->count(20)->create(); // 50개의 레코드를 생성

        Product::find(1)->update(['price' => 1000, 'delivery_fee' => 1000]);
        ProductOption::whereIn('id', [1, 2])->update(['price' => 1000, 'stock_quantity' => 1000]);
    }
}
