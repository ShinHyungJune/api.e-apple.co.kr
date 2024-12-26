<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProductReview extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;
    //
    const IMAGES = 'images';

    const TYPE_TEXT = 'text';

    const TEXT_REVIEW_POINTS = 500;

    const TYPE_PHOTO = 'photo';

    const PHOTO_REVIEW_POINTS = 1500;
    const AVAILABLE_DAYS = 30;//주문 후 30일 이내 리뷰작성 가능


    protected $guarded = ['id'];


    public function getDepositPoints()
    {
        if ($this->type === self::TYPE_PHOTO) {
            return [self::PHOTO_REVIEW_POINTS, '포토리뷰 작성'];
        }
        if ($this->type === self::TYPE_TEXT) {
            return [self::TEXT_REVIEW_POINTS, '리뷰 작성'];
        }
        return null;
    }

    public function getWithdrawalPoints()
    {
        if ($this->type === self::TYPE_PHOTO) {
            return [self::PHOTO_REVIEW_POINTS, '포토리뷰 삭제'];
        }
        if ($this->type === self::TYPE_TEXT) {
            return [self::TEXT_REVIEW_POINTS, '리뷰 삭제'];
        }
    }



    public function scopeSearch(Builder $query, $filters)
    {
        if (!empty($filters['type'])) {
            if ($filters['type'] === self::TYPE_PHOTO) {
                $query->has('media')->whereHas('media', function ($query) {
                    $query->where('mime_type', 'like', 'image%');
                });
            }
            if ($filters['type'] === self::TYPE_TEXT) {
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

    public function points()
    {
        return $this->morphMany(Point::class, 'model');
    }

    public function getTypeAttribute()
    {
        return $this->has('media') ? self::TYPE_PHOTO : self::TYPE_TEXT;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productOption()
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class, 'order_product_id');
    }

    public function scopeMine(Builder $query)
    {
        $query->where('user_id', auth()->id());
    }

}
