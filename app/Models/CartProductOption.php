<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartProductOption extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function productOption(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class);
    }

    public function scopeMine(Builder $query, $request)
    {
        //$query->where('user_id', auth()->id());
        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            if (!($request->guest_id)) abort(403, '비회원 아이디가 없습니다.');
            $query->where('guest_id', $request->guest_id);
        }
    }

}
