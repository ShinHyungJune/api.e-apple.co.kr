<?php

namespace Database\Seeders;

use App\Models\Code;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('codes')->truncate();
        DB::table('codes')->insert([
            ['id' => 1, 'parent_id' => 0, 'left_id' => 0, 'right_id' => 0, 'order' => 0, 'name' => '과일카테코리', 'is_use' => true, 'is_display' => true],
            ['id' => 2, 'parent_id' => 1, 'left_id' => 0, 'right_id' => 0, 'order' => 0, 'name' => '국산과일', 'is_use' => true, 'is_display' => true],
            ['id' => 3, 'parent_id' => 2, 'left_id' => 0, 'right_id' => 0, 'order' => 0, 'name' => '딸기', 'is_use' => true, 'is_display' => true],
            ['id' => 4, 'parent_id' => 2, 'left_id' => 0, 'right_id' => 0, 'order' => 0, 'name' => '사과', 'is_use' => true, 'is_display' => true],
            ['id' => 5, 'parent_id' => 1, 'left_id' => 0, 'right_id' => 0, 'order' => 0, 'name' => '수입과일', 'is_use' => true, 'is_display' => true],
            ['id' => 6, 'parent_id' => 5, 'left_id' => 0, 'right_id' => 0, 'order' => 0, 'name' => '바나나', 'is_use' => true, 'is_display' => true],
            ['id' => 7, 'parent_id' => 5, 'left_id' => 0, 'right_id' => 0, 'order' => 0, 'name' => '아보카도', 'is_use' => true, 'is_display' => true],
            ['id' => 8, 'parent_id' => 1, 'left_id' => 0, 'right_id' => 0, 'order' => 0, 'name' => '제철과일', 'is_use' => true, 'is_display' => true],


            /*['id' => 9, 'parent_id' => 0, 'left_id' => 0, 'right_id' => 0, 'order' => 0, 'name' => '이달의 추천 상품', 'is_use' => true, 'is_display' => true],
            ['id' => null, 'parent_id' => 9, 'left_id' => 0, 'right_id' => 0, 'order' => 0, 'name' => '국산', 'is_use' => true, 'is_display' => true],
            ['id' => null, 'parent_id' => 9, 'left_id' => 0, 'right_id' => 0, 'order' => 0, 'name' => '수입', 'is_use' => true, 'is_display' => true],
            ['id' => null, 'parent_id' => 9, 'left_id' => 0, 'right_id' => 0, 'order' => 0, 'name' => '제철', 'is_use' => true, 'is_display' => true],
            ['id' => null, 'parent_id' => 9, 'left_id' => 0, 'right_id' => 0, 'order' => 0, 'name' => '가공품', 'is_use' => true, 'is_display' => true],
            ['id' => null, 'parent_id' => 9, 'left_id' => 0, 'right_id' => 0, 'order' => 0, 'name' => '대용량', 'is_use' => true, 'is_display' => true],
            ['id' => null, 'parent_id' => 9, 'left_id' => 0, 'right_id' => 0, 'order' => 0, 'name' => '소용량', 'is_use' => true, 'is_display' => true],*/
        ]);
        Code::rebuild(0, 0);
    }
}
