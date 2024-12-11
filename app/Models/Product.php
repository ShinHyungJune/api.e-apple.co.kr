<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    const IMAGES =  'product_images';
    const DESC_IMAGES =  'product_desc_images';

    protected $casts = [
        'categories' => 'array',
        'tags' => 'array',
    ];

    protected $guarded = ['id'];


    public function scopeCategory(Builder $query, $category)
    {
        if ($category) {
            $query->whereJsonContains('categories', $category);
        }
    }

    public function scopeSearch(Builder $query, $filters)
    {
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        //TAGS 실시간 인기, 클래식 과일, 어른을 위한 픽, 추가 증정
        if (!empty($filters['tags'])) {
            $tags = $filters['tags'];
            $query->where(function ($query) use ($tags) {
                foreach ($tags as $tag) {
                    $query->orWhereJsonContains('tags', $tag);
                }
            });
        }
    }

    public function registerMediaConversions(Media|null $media = null): void
    {
        $this->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function inquiries()
    {
        return $this->hasMany(ProductInquiry::class);
    }

    /*public function mdPackages()
    {
        return $this->belongsToMany(MdProductPackage::class, 'product_package_product')
            ->withPivot('quantity') // 중간 테이블의 추가 필드
            ->withTimestamps();
    }*/

}
