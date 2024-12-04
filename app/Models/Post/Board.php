<?php

namespace App\Models\Post;

use App\Models\PointPolicy;
use App\Policies\PostPolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;

    const NOTICE_BOARD_ID = 1;
    const FAQ_BOARD_ID = 2;
    const STORY_BOARD_ID = 3;
    const EVENT_BOARD_ID = 3;

    /*INDEX,CREATE,STORE,SHOW,EDIT,UPDATE,DESTROY => VIEWANY,CREATE*/

    public function getCanViewAnyAttribute()
    {

    }

    public function getCanCreateAttribute()
    {
        return (new PostPolicy)->create(auth()->user(), $this);
    }

    public function categoryItems()
    {
        return $this->hasMany(BoardCategory::class);//->selectRaw("id value, name text, board_id, deleted_at");
    }

}
