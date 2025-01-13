<?php

namespace App\Models;

use App\Enums\ProductPackageType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductPackage extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    const IMAGES = 'imgs';

    protected $guarded = ['id'];

    protected $casts = [
        'type' => ProductPackageType::class,
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_package_product')
            //->withPivot('quantity')
            ->withTimestamps();
    }

    public function registerMediaConversions(Media|null $media = null): void
    {
        $this->addMediaConversion('preview')
            ->fit(Fit::Contain, 400, 300)
            ->nonQueued();
    }

    public function scopeSearch(Builder $query, $filters)
    {
        if (isset($filters['keyword'])) {
            $query->where('title', 'like', '%' . $filters['keyword'] . '%');
        }
    }
}
