<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PopBanner extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $guarded = ['id'];

    const IMAGE = 'img';

    protected $casts = [
        'is_active' => 'boolean',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->quality(80);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::IMAGE)
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }


    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('started_at', '<=', now())
            ->where('finished_at', '>=', now());
    }

    public function scopeSearch(Builder $query, $filters): Builder
    {
        if (isset($filters['keyword'])) {
            $query->where('title', 'like', '%' . $filters['keyword'] . '%');
        }
        return $query;
    }
}
