<?php

namespace Database\Seeders;

use App\Models\Post\Board;
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
            ['id' => 1, 'board_id' => Board::STORY_BOARD_ID, 'name' => '이달의 과일', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),],
            ['id' => 2, 'board_id' => Board::STORY_BOARD_ID, 'name' => '현장 리포트', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),],
            ['id' => 3, 'board_id' => Board::STORY_BOARD_ID, 'name' => '과일이야기', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),],

            ['id' => 4, 'board_id' => Board::FAQ_BOARD_ID, 'name' => '주문결제', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),],
            ['id' => 5, 'board_id' => Board::FAQ_BOARD_ID, 'name' => '배송', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),],
            ['id' => 6, 'board_id' => Board::FAQ_BOARD_ID, 'name' => '상품', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),],
            ['id' => 7, 'board_id' => Board::FAQ_BOARD_ID, 'name' => '취소/교환/환불', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),],
        ]);
    }
}
