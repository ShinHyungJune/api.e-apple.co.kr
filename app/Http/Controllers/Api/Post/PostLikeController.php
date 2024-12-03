<?php

namespace App\Http\Controllers\Api\Post;

use App\Http\Controllers\Controller;
use App\Models\Post\Post;
use App\Models\Post\PostComment;
use App\Models\Post\PostLike;
use Illuminate\Support\Facades\DB;

class PostLikeController extends Controller
{
    public function store($likeableType, $id, $type)
    {
        if (!in_array($likeableType, PostLike::LIKEABLE_TYPES)) {
            abort(500, '좋아요 할 수 없는 타입 입니다.');
        }
        if (!in_array($type, PostLike::TYPES)) {
            abort(500, '좋아요 할 수 없는 타입 입니다.');
        }

        $model = new \stdClass();
        if ($likeableType === 'post') $model = Post::with('comments.user')->findOrFail($id);
        if ($likeableType === 'comment') $model = PostComment::with(['user'])->findOrFail($id);

        $model = DB::transaction(function () use ($model, $type) {
            $like = $model->likes()->where('user_id', auth()->user()->id)->where('type', $type)->first();
            if ($like) {
                abort(500, '이미 ' . PostLike::getTypeName($type) . '하였습니다.');
                //TOGGLE 기능
                /*$like->delete($like);
                $model->decrement($type . '_count');*/
            }
            else {
                $model->likes()->create([
                    'user_id' => auth()->user()->id,
                    'to_user_id' => $model->created_by,
                    'type' => $type,
                ]);
                $model->increment($type . '_count');
            }
            return $model;
        });

        return response()->success(compact(['model']));
    }
}
