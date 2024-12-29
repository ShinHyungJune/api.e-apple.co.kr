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
        Sweetness::factory()->count(10)->create(); // 100개의 레코드를 생성
    }
}
