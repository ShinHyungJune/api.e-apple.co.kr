<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('boards', function (Blueprint $table) {
            $table->id()->comment('기본키');
            $table->string('name_en', 50)->comment('게시판 영어 이름');
            $table->string('name_ko', 50)->comment('게시판 이름');
            $table->string('desc')->nullable();
            $table->enum('type', ['L', 'G', 'Q', 'E'])->default('L');//게시판유형(리스트형,갤러리형,답변형,이벤트)
            $table->unsignedTinyInteger('per_page');//POST COUNT FOR PAGE
            $table->string('skin');//게시판 스킨
            $table->string('layout', 10);//게시판레이아웃(sub,noleft,blank)
            $table->boolean('is_use_editor')->default(false);//EDITOR 사용
            $table->boolean('is_use_notice_top')->default(false);//상단공지 사용
            $table->boolean('is_use_comment')->default(false);//댓글 사용
            $table->boolean('is_use_secret')->default(false);//비밀글 사용
            $table->boolean('is_use_file')->default(false);//첨부파일 사용
            $table->boolean('is_use_category')->default(false);//카테고리 사용
            $table->unsignedTinyInteger('file_count');//첨부파일 개수
            $table->unsignedInteger('file_size');//첨부파일 용량
            $table->string('file_ext'); //첨부파일 확장자
            $table->unsignedTinyInteger('level_list');//
            $table->unsignedTinyInteger('level_view');//
            $table->unsignedTinyInteger('level_create');//
            $table->unsignedTinyInteger('level_comment');//
            $table->unsignedTinyInteger('level_upload');//
            $table->unsignedTinyInteger('level_download');//
            $table->timestamps();
        });
        /**
         * truncate table `boards`;
         * INSERT INTO `boards` VALUES
        (1,'notice','공지사항','공지사항 게시판','L',10,'basic','sub',0,1,1,0,1,0,5,2097152,'.zip,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.pdf,.hwp,image/*',0,0,0,0,0,0, now(), now()),
        (2,'download','자료실','자료실 게시판','L',10,'basic','sub',0,0,1,0,1,0,5,2097152,'.zip,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.pdf,.hwp,image/*',0,0,0,0,0,0, now(), now()),
        (3,'post','게시판','게시판','L',10,'basic','sub',0,0,1,0,1,0,5,2097152,'.zip,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.pdf,.hwp,image/*',0,0,0,0,0,0, now(), now()),
        (4,'qna','질문답변','게시판','L',10,'basic','sub',0,0,1,0,1,0,5,2097152,'.zip,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.pdf,.hwp,image/*',0,0,0,0,0,0, now(), now())
         */
        Schema::create('board_categories', function (Blueprint $table) {
            $table->id()->comment('기본키');
            $table->foreignId('board_id')->comment('board 기본키');
            $table->string('name')->comment('카테고리명');
            $table->timestamps();
        });
        /**
         * truncate table `board_categories`;
         * INSERT INTO `board_categories` VALUES (1,1,'공지',now(),now()),(2,1,'질문',now(),now());
         */
        Schema::create('posts', function (Blueprint $table) {
            $table->id()->comment('기본키');
            $table->foreignId('board_id')->comment('board 기본키');
            $table->unsignedInteger('category_id')->nullable()->comment('카테고리 기본키');
            $table->string('title')->comment('제목');
            $table->text('content')->comment('내용');
            $table->text('content_answer')->nullable()->comment('답변내용');
            $table->boolean('is_notice')->default(false)->comment('공지사항 여부');
            $table->boolean('is_notice_top')->default(false)->comment('상단 공지사항 여부');
            $table->boolean('is_html')->default(false)->comment('내용 html 여부');
            $table->boolean('is_secret')->default(false)->comment('비밀글 여부');
            $table->boolean('is_popup')->default(false)->comment('팝업 여부');
            $table->date('start_date')->nullable()->comment('시작일');
            $table->date('end_date')->nullable()->comment('종료일');
            $table->unsignedInteger('read_count')->default(0)->comment('조회수');
            $table->unsignedInteger('comment_count')->default(0)->comment('답글숫');
            $table->unsignedInteger('like_count')->default(0)->comment('좋아요 개수');
            $table->unsignedInteger('dislike_count')->default(0)->comment('싫어요 개수');
            $table->unsignedInteger('created_by')->comment('작성자 아이디');
            $table->unsignedInteger('updated_by')->nullable()->comment('수정자 아이디');
            $table->unsignedInteger('deleted_by')->nullable()->comment('삭제자 아이디');
            $table->timestamp('answered_at')->nullable()->comment('답변일시');
            $table->unsignedInteger('answered_by')->nullable()->comment('답변자 아이디');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('post_comments', function (Blueprint $table) {
            $table->id()->comment('기본키');
            $table->foreignId('post_id')->comment('post 기본키');
            $table->text('content')->comment('내용');
            $table->unsignedInteger('like_count')->default(0)->comment('좋아요 개수');
            $table->unsignedInteger('dislike_count')->default(0)->comment('싫어요 개수');
            $table->unsignedInteger('created_by')->comment('작성자 아이디');
            $table->unsignedInteger('updated_by')->nullable()->comment('수정자 아이디');
            $table->unsignedInteger('deleted_by')->nullable()->comment('삭제자 아이디');
            $table->timestamps();
            $table->softDeletes();
        });

        /* 라이브러리로 대체 https://spatie.be/docs/laravel-medialibrary/v11/introduction
        Schema::create('post_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id');
            $table->string('origin_name');
            $table->string('saved_name');
            $table->string('path');
            $table->unsignedInteger('size');
            $table->string('type');
            $table->unsignedInteger('down_count')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });*/

        /*Schema::create('post_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->unsignedBigInteger("to_user_id");
            //$table->unsignedBigInteger("likeable_id"); $table->string("likeable_type"); $table->index(["likeable_id", "likeable_type"]);
            $table->morphs('likeable');
            $table->enum('type', ['like', 'dislike']);
            $table->timestamps();

            $table->unique(['user_id', 'likeable_id', 'likeable_type', 'type']);
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boards');
        Schema::dropIfExists('board_categories');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('post_comments');
        //Schema::dropIfExists('post_files');
        //Schema::dropIfExists('post_likes');
    }
};
