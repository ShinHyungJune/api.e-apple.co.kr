<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Inquiry extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    const IMAGES = 'imgs';
    protected $guarded = ['id'];

    public function scopeSearch(Builder $query, $filters)
    {
        if (isset($filters['is_answered'])) {
            if ($filters['is_answered']) {
                $query->whereNotNull('answered_at');
            } else {
                $query->whereNull('answered_at');
            }
        }

        if (isset($filters['keyword'])) {
            $query->where('type', 'like', '%' . $filters['keyword'] . '%')
                ->orWhere('content', 'like', '%' . $filters['keyword'] . '%');
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
