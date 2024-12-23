<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cartProductOptions(): HasMany
    {
        return $this->hasMany(CartProductOption::class);
    }

    public function scopeMine(Builder $query, $request)
    {
        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            if (!($request->guest_id)) abort(403, '비회원 아이디가 없습니다.');
            $query->where('guest_id', $request->guest_id);
        }
    }

    public function updateOrCreateProductOptions($data)
    {
        //$cartProductOptions = [];
        foreach ($data['product_options'] as $productOption) {
            $option = $this->product->options->findOrFail($productOption['product_option_id']);
            /*$cartProductOptions[] = [
                'user_id' => auth()->id() ?? null,
                'guest_id' => $data['guest_id'] ?? null,
                'product_option_id' => $option->id,
                'price' => $option->price,
                'quantity' => $productOption['quantity'],
            ];*/
            $cartProductOption = CartProductOption::updateOrCreate(
                ['cart_id' => $this->id, 'product_option_id' => $option->id],
                ['user_id' => auth()->id() ?? null, 'guest_id' => $data['guest_id'] ?? null, 'price' => $option->price]
            );
            $cartProductOption->increment('quantity', $productOption['quantity']);
        }
        //$cart->cartProductOptions()->createMany($cartProductOptions);
    }
}
