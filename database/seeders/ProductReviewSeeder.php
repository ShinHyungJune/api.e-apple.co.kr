<?php

namespace Database\Seeders;

use App\Models\ProductReview;
use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductReview::factory()->count(50)->create(); // 50개의 레코드를 생성
    }
}
