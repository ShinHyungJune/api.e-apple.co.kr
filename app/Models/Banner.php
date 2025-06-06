<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Banner extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = ['id'];

    const IMAGES = 'imgs';

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeSearch(Builder $query, $filters)
    {
        if (isset($filters['keyword'])) {
            $query->where('title', 'like', '%' . $filters['keyword'] . '%');
        }
    }

}
