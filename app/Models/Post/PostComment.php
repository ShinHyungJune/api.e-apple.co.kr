<?php

namespace App\Models\Post;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PostComment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function scopeMine($query)
    {
        $query->where('created_by', Auth::user()->id);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function likes()
    {
        return $this->morphMany(PostLike::class, 'likeable');
    }

}
