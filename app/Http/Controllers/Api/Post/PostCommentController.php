<?php

namespace App\Http\Controllers\Api\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\PostCommentRequest;
use App\Models\Post\Post;
use App\Models\Post\PostComment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostCommentController extends Controller
{

    public function store(PostCommentRequest $request, $postId)
    {
        $data = $request->validated();
        $comment = DB::transaction(function () use ($postId, $data) {
            $post = Post::findOrFail($postId);
            $post->increment('comment_count');
            $comment = $post->comments()->create($data);
            $comment->user_name = auth()->user()->name;
            return $comment;
        });
        return response()->success(['comment' => $comment]);
    }

    public function destroy($postId, $id)
    {
        $post = Post::findOrFail($postId);
        $query = PostComment::query();
        if (!Auth::user()->isAdmin()) {
            $query->mine($id);
        }
        $query->where('post_id', $postId)->findOrFail($id)->delete();
        $post->decrement('comment_count');
        return response()->success();
    }

}
