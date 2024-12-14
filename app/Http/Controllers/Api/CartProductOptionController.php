<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CartProductOptionRequest;
use App\Http\Resources\CartResource;
use App\Models\CartProductOption;

/**
 * @group Cart Product Option(장바구니 상품 옵션)
 */
class CartProductOptionController extends ApiController
{

    /**
     * 저장
     * @priority 1
     * @responseFile storage/responses/cart.json
     */
    public function store(CartProductOptionRequest $request, $cartId)
    {
        $data = $request->validated();

        $cart = auth()->user()->carts()->findOrFail($cartId)->load('product');
        $data['price'] = $cart->product->price;
        $cart->cartProductOptions()->create($data);

        return $this->respondSuccessfully(CartResource::make($cart));
    }

    /**
     * 수량 변경
     * @priority 1
     */
    public function update(CartProductOptionRequest $request, $cartId, $optionId)
    {
        $data = $request->validated();
        $options = auth()->user()->cartProductOptions()->where('cart_id', $cartId)->findOrFail($optionId);
        $options->update($data);
        return $this->respondSuccessfully();
    }

    /**
     * 삭제
     * @priority 1
     */
    public function destroy($cartId, $optionId)
    {
        //auth()->user()->cartProductOptions()->where('cart_id', $cartId)->findOrFail($optionId)->delete();
        CartProductOption::mine()->where('cart_id', $cartId)->delete($optionId);
        return $this->respondSuccessfully();
    }

}
