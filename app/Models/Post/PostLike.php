<?php

namespace App\Models\Post;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    use HasFactory;

    const LIKEABLE_TYPES = ['post', 'comment'];

    const TYPE_LIKE = 'like';
    const TYPE_DISLIKE = 'dislike';
    const TYPES = ['like', 'dislike'];
    protected $guarded = ['id'];

    public static function getTypeName($type)
    {
        if ($type === self::TYPE_LIKE) return '추천';
        if ($type === self::TYPE_DISLIKE) return '비추천';
    }
}
