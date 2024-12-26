<?php

namespace Database\Seeders;

use App\Models\ProductInquiry;
use Illuminate\Database\Seeder;

class ProductInquirySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductInquiry::factory()->count(20)->create(); // 50개의 레코드를 생성
    }
}
