<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MdProductPackage extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    const IMAGES = 'images';

    protected $guarded = ['id'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'md_product_package_product')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function registerMediaConversions(Media|null $media = null): void
    {
        $this->addMediaConversion('preview')
            ->fit(Fit::Contain, 400, 300)
            ->nonQueued();
    }


}
