<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('boards')->truncate();
        DB::table('boards')->insert([
            ['id' => 1, 'name_en' => 'notice', 'name_ko' => '공지사항', 'desc' => '공지사항 게시판', 'type' => 'L', 'per_page' => 10, 'skin' => 'basic', 'layout' => 'sub', 'is_use_editor' => 0, 'is_use_notice_top' => 1, 'is_use_comment' => 1, 'is_use_secret' => 0, 'is_use_file' => 1, 'is_use_category' => 0, 'file_count' => 5, 'file_size' => 2097152, 'file_ext' => '.zip,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.pdf,.hwp,image/*',
                'level_list' => 0, 'level_view' => 0, 'level_create' => 1, 'level_comment' => 0, 'level_upload' => 0, 'level_download' => 0, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),],
            ['id' => 2, 'name_en' => 'faq', 'name_ko' => 'FAQ', 'desc' => 'FAQ', 'type' => 'L', 'per_page' => 10, 'skin' => 'basic', 'layout' => 'sub', 'is_use_editor' => 0, 'is_use_notice_top' => 0, 'is_use_comment' => 1, 'is_use_secret' => 0, 'is_use_file' => 1, 'is_use_category' => 0, 'file_count' => 5, 'file_size' => 2097152, 'file_ext' => '.zip,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.pdf,.hwp,image/*',
                'level_list' => 0, 'level_view' => 0, 'level_create' => 1, 'level_comment' => 0, 'level_upload' => 0, 'level_download' => 0, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),],
            ['id' => 3, 'name_en' => 'story', 'name_ko' => '스토리', 'desc' => '스토리게시판', 'type' => 'L', 'per_page' => 10, 'skin' => 'basic', 'layout' => 'sub', 'is_use_editor' => 0, 'is_use_notice_top' => 0, 'is_use_comment' => 1, 'is_use_secret' => 0, 'is_use_file' => 1, 'is_use_category' => 0, 'file_count' => 5, 'file_size' => 2097152, 'file_ext' => '.zip,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.pdf,.hwp,image/*',
                'level_list' => 0, 'level_view' => 0, 'level_create' => 1, 'level_comment' => 0, 'level_upload' => 0, 'level_download' => 0, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),],
            ['id' => 4, 'name_en' => 'event', 'name_ko' => '이벤트', 'desc' => '이벤트게시판', 'type' => 'L', 'per_page' => 10, 'skin' => 'basic', 'layout' => 'sub', 'is_use_editor' => 0, 'is_use_notice_top' => 0, 'is_use_comment' => 1, 'is_use_secret' => 0, 'is_use_file' => 1, 'is_use_category' => 0, 'file_count' => 5, 'file_size' => 2097152, 'file_ext' => '.zip,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.pdf,.hwp,image/*',
                'level_list' => 0, 'level_view' => 0, 'level_create' => 1, 'level_comment' => 0, 'level_upload' => 0, 'level_download' => 0, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),],
        ]);
    }
}
