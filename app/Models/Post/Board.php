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
    const QNA_BOARD_ID = 4;
    const POST_BOARD_ID = 3;
    const POINT_BOARD_ID = 5;
    const EVENT_BOARD_ID = 6;
    const TYPE_QNA = 'Q';

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
