<?php

namespace App\Http\Controllers\Api\Post;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Post\PostRequest;
use App\Http\Resources\Post\BoardResource;
use App\Http\Resources\Post\PostResource;
use App\Models\Post\Board;
use App\Models\Post\Post;
use App\Models\Post\PostFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

/**
 * @group 게시판(공지사항: 1, FAQ: 2 , 스토리: 3, 이벤트: 4)
 */
class PostController extends ApiController
{

    /**
     * 게시판설정
     * @priority 1
     * @unauthenticated
     * @responseFile storage/responses/board.json
     */
    public function init(int $id)
    {
        $board = Board::with(['categoryItems'])->findOrFail($id)->append(['can_create']);
        return BoardResource::make($board);
    }

    /**
     * 목록
     * @priority 1
     * @unauthenticated
     * @responseFile storage/responses/posts.json
     */
    public function index(Request $request, $id)
    {
        $board = Board::findOrFail($id);
        //$boardCategories = BoardCategory::select('name as text', 'id as value')->where('board_id', $board->id)->get();
        Gate::authorize('viewAny', [Post::class, $board->type]);

        $filters = $request->only(['category_id']);

        //Media Library
        $query = Post::with(['user', 'comments.user'/*, 'files'*/])->where('board_id', $board->id)->search($filters);
        $items = $query->orderByRaw("FIELD(is_notice, true, false)")->latest()->paginate($request->take ?? 10);
        $items->map(fn($e) => $e->append(['can_view', 'can_update', 'can_delete'/*, 'image_url'*/]));

        return PostResource::collection($items);
    }

    /**
     * 생성
     * @priority 1
     * @unauthenticated
     * @responseFile storage/responses/post.json
     */
    public function store(PostRequest $request)
    {
        $data = $request->validated();
        $files = $request->file('files');
        $board = Board::findOrFail($data['board_id']);
        Gate::authorize('create', [Post::class, $board]);

        $post = DB::transaction(function () use ($board, $data, $files) {
            $post = tap(new Post($data))->save();
            //Media Library
            //if ($files) PostFile::stores($post, $files);
            foreach ($files as $file) {
                $post->addMedia($file)->toMediaCollection(Post::MEDIA_COLLECTION);
            }
            return $post;
        });
        return $this->respondSuccessfully(PostResource::make($post));
    }



















    public function show($id)
    {
        //TODO Media Library
        $post = Post::with(['user', /*'files',*/ 'comments.user'])->findOrFail($id);
        Gate::authorize('view', $post);
        $post->increment('read_count');
        //$post->append('images');
        return $this->respondSuccessfully(PostResource::make($post));
    }

    public function update(PostRequest $request, $id)
    {
        $query = Post::query();
        //if (!Auth::user()->isAdmin()) $query->mine($id);
        $post = $query->findOrFail($id);
        //$answeredAt = $post->answered_at;
        Gate::authorize('update', $post);

        $data = $request->validated();
        $files = $request->file('files');

        $post = DB::transaction(function () use ($post, $data, $files) {
            $post = tap($post)->update($data);
            /*if ($files) {
                PostFile::stores($post, $files);
            }*/
            return $post;
        });

        return response()->success($post);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        Gate::authorize('delete', $post);

        DB::transaction(function () use ($post, $id) {

            $oFiles = PostFile::where('post_id', $id);
            $files = $oFiles->get();
            foreach ($files as $file) {
                Storage::disk('public')->delete($file->path);
            }
            $oFiles->delete();

            if (auth()->user()->isAdmin()) {
                foreach ($post->event_users as $eventUser) {
                    $post->destroyEventUser($eventUser);
                }
            }


            $post->delete($id);
        });

        return response()->success();
    }

    public function showFile($id)
    {
        $file = PostFile::findOrFail($id);
        $path = Storage::disk('local')->path($file->path);
        return response()->file($path);
    }

    public function destroyFile($id)
    {
        $file = PostFile::findOrFail($id);
        Storage::disk('local')->delete($file->path);
        $file->delete();
        return response()->success();
    }

    /*
    public function storeFile(Request $request)
    {
        try {
            $file = $request->file('file');
            $path = Storage::disk('public')->put('post', $file);
            Log::debug($path);

            $dbFile = new PostFile();
            $dbFile->origin_name = $file->getClientOriginalName();
            $dbFile->saved_name = $file->getClientOriginalName();
            $dbFile->path = $path;
            $dbFile->size = $file->getSize();
            $dbFile->type = $file->getClientMimeType();
            $dbFile->save();

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

        return response()->json(['id' => $dbFile->id], 200);
    }

    public function notices()
    {
        $notices = Post::where('board_id', Board::POST_BOARD_ID)->where('notice', 'Y')->latest()->get();
        return response()->json(compact('notices'));
    }*/

}
