<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\UserResource;
use App\Models\Post\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        $id = $this->id;
        $return = [
            ...$this->only([
                'id', 'board_id', 'category_id', 'title', 'content', 'content_answer',
                'is_notice', 'is_notice_top', 'is_secret',
                //'is_html', 'is_popup',
                'start_date', 'end_date',
                'read_count', 'comment_count',
                'created_at', 'updated_at',
                'can_view', 'can_update', 'can_delete'
            ]),
            'user' => UserResource::make($this->user),
            'files' => $this->getMedia(Post::MEDIA_COLLECTION) ? PostFileResource::collection($this->getMedia(Post::MEDIA_COLLECTION)) : null,
            'comments' => PostCommentResource::collection($this->comments),
        ];

        /*
        return $return;
         /*/
        $comments = [
            'created_at' => '등록일', 'updated_at' => '수정일',
            'can_view' => '조회가능여부', 'can_update' => '수정가능여부', 'can_delete' => '삭제가능여부',
            //'user' => '작성자',
            //'files' => '첨부파일',
            //'comments' => '댓글목록'
        ];
        return getScribeResponseFile($return, 'posts', $comments);
        //*/

    }
}
