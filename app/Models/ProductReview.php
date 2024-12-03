<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductReview extends Model implements HasMedia
{
    use InteractsWithMedia;
    //
    const IMAGES = 'images';

    protected $guarded = ['id'];

    public function scopeSearch(Builder $query, $filters)
    {
        if (!empty($filters['type'])) {
            if ($filters['type'] === 'photo') {
                $query->has('media')
                    ->whereHas('media', function ($query) {
                        $query->where('mime_type', 'like', 'image%');
                    });
            }
            if ($filters['type'] === 'text') {
                $query->whereDoesntHave('media', function ($query) {
                    $query->where('mime_type', 'like', 'image%'); // 이미지 미디어가 아닌 경우만 필터링
                });
            }
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
