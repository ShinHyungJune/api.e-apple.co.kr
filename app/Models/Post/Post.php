<?php

namespace App\Models\Post;

use App\Models\User;
use App\Policies\PostPolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Post extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    const MEDIA_COLLECTION = 'posts';

    protected $guarded = ['id'];

    //protected $appends = ['mobile_title'/*,'content_html'*/];


    public function board()
    {
        return $this->belongsTo(Board::class);
    }








    /*public function files()
    {
        return $this->hasMany(PostFile::class);
    }

    public function getImageUrlAttribute()
    {
        foreach ($this->files as $v) {
            $types = explode('/', $v->type);
            if (isset($types[0]) && $types[0] === 'image') {
                //return url($v->path);
                return url('/api/posts/files/' . $v->id);
            }
        }
    }

    public function getImagesAttribute()
    {
        $results = [];
        foreach ($this->files as $v) {
            $types = explode('/', $v->type);
            if (isset($types[0]) && $types[0] === 'image') {
                $results[] = [...$v->toArray(), 'url' => url('/api/posts/files/' . $v->id)];
            }
        }
        return $results;
    }*/

    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function likes()
    {
        return $this->morphMany(PostLike::class, 'likeable');
    }

    public function scopeSearch($query, $searches)
    {
        if (isset($searches->keyword) && $searches->keyword) {
            $query->where('title', 'like', '%' . $searches->keyword . '%');
            $query->orWhereHas('user', function ($query) use ($searches) {
                $query->where('email', 'like', '%' . $searches->keyword . '%');
            });
        }
        /*if ($request->search) {
            $searches = json_decode($request->search);
            //dd($searches);
            if (isset($searches->keyword) && $searches->keyword) {
                $query->whereHas('member', function ($query) use ($searches) {
                    $query->where('name', 'like', '%' . $searches->keyword . '%')->orWhere('mobile', 'like', '%' . $searches->keyword . '%');
                });
            }
            if ($searches->dates && $searches->dates[0] && $searches->dates[1]) {
                $query->whereBetween(DB::raw('DATE(created_at)'), [$searches->dates]);
            }
        }*/
    }

    public function getMobileTitleAttribute()
    {
        return $this->title . '<br/>' . $this->created_at . '|' . $this->user->name . '|조회' . $this->read_count;
    }


    public function getContentHtmlAttribute()
    {
        /*$this->attributes['content'] = "
            test
            http://google.com
            test
            https://google.com
            www.google.com
            test
        ";*/
        $pattern = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
        return nl2br(preg_replace($pattern, '<a href="$0" target="_blank">$0</a>', $this->attributes['content']));
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function scopeMine($query)
    {
        $query->where('created_by', Auth::user()->id);
    }

    public function scopeQna($query)
    {
        $query->where('board_id', Board::QNA_BOARD_ID);
    }

    public function scopeIsPopup($query)
    {
        $query->where('is_popup', true);
    }


    /*INDEX,CREATE,STORE,SHOW,EDIT,UPDATE,DESTROY => VIEW,UPDATE,DELETE*/
    public function getCanViewAttribute()
    {
        return (new PostPolicy)->view(auth()->user(), $this);
    }

    public function getCanUpdateAttribute()
    {
        return (new PostPolicy)->update(auth()->user(), $this);
    }

    public function getCanDeleteAttribute()
    {
        return (new PostPolicy)->view(auth()->user(), $this);
    }


    public function event_users()
    {
        return $this->hasMany(PostEventUser::class);
    }

    public function destroyEventUser($eventUser)
    {
        //차감 포인트 복구
        User::findOrFail($eventUser->user_id)->depositPoint(PointTypes::ADMIN_DEPOSIT,
            $this->id . '번 ' . PointTypes::EVENT_COST->getText() . '복구',
            $this->event_cost_points ?? 0);

        //이벤트 참가신청 삭제
        $eventUser->delete();
    }

}
