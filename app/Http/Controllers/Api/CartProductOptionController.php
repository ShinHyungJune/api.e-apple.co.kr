<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CartProductOptionRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartProductOption;
use Illuminate\Http\Request;

/**
 * @group Cart Product Option(장바구니 상품 옵션)
 */
class CartProductOptionController extends ApiController
{

    /**
     * 저장
     * @priority 1
     * @unauthenticated
     * @responseFile storage/responses/cart.json
     */
    public function store(CartProductOptionRequest $request, $cartId)
    {
        $data = $request->validated();

        $cart = Cart::mine($request)->findOrFail($cartId)->load('product');
        $data['price'] = $cart->product->price;
        $cart->cartProductOptions()->create($data);

        return $this->respondSuccessfully(CartResource::make($cart));
    }

    /**
     * 수량 변경
     * @unauthenticated
     * @queryParam guest_id 비회원 아이디 Example: 1
     * @priority 1
     */
    public function update(CartProductOptionRequest $request, $cartId, $optionId)
    {
        $data = $request->validated();
        $options = CartProductOption::mine($request)->where('cart_id', $cartId)->findOrFail($optionId);
        $options->update($data);
        return $this->respondSuccessfully();
    }

    /**
     * 삭제
     * @unauthenticated
     * @queryParam guest_id 비회원 아이디 Example: 1
     * @priority 1
     */
    public function destroy(Request $request, $cartId, $optionId)
    {
        //auth()->user()->cartProductOptions()->where('cart_id', $cartId)->findOrFail($optionId)->delete();
        CartProductOption::mine($request)->where('cart_id', $cartId)->findOrFail($optionId)->delete();
        return $this->respondSuccessfully();
    }

}
