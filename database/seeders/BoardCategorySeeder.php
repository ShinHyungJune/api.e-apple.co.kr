<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BoardCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('board_categories')->truncate();
        DB::table('board_categories')->insert([
            ['id' => 1, 'board_id' => 3, 'name' => '이달의 과일', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),],
            ['id' => 2, 'board_id' => 3, 'name' => '현장 리포트', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),],
            ['id' => 3, 'board_id' => 3, 'name' => '과일이야기', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),],
        ]);
    }
}
