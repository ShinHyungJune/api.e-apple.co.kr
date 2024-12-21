<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @group Cart(장바구니)
 */
class CartController extends ApiController
{

    /**
     * 목록
     * @priority 1
     * @unauthenticated
     * @queryParam guest_id 비회원 아이디 Example: guestId
     * @responseFile storage/responses/carts.json
     */
    public function index(Request $request)
    {
        $items = Cart::mine($request)->with(['product', 'cartProductOptions.productOption'])->paginate($request->take ?? 10);
        return CartResource::collection($items);
    }

    /**
     * 저장
     * @priority 1
     * @unauthenticated
     * @responseFile storage/responses/cart.json
     */
    public function store(CartRequest $request)
    {
        $cart = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $cart = Cart::mine($request)->updateOrCreate(['product_id' => $data['product_id']], $data);
            $cart->load('product.options');
            $cart->updateOrCreateProductOptions($data);
            return $cart->load('cartProductOptions.productOption');
        });

        return $this->respondSuccessfully(CartResource::make($cart));
    }


    /**
     * 수정(장바구니 상품 옵션 추가)
     * @priority 1
     * @unauthenticated
     * @responseFile storage/responses/cart.json
     */
    public function update(CartRequest $request, $id)
    {
        $cart = DB::transaction(function () use ($request, $id) {
            $data = $request->validated();
            $cart = Cart::with(['product.options'])->mine($request)->findOrFail($id);
            $cart->updateOrCreateProductOptions($data);
            return $cart->load('cartProductOptions.productOption');
        });

        return $this->respondSuccessfully(CartResource::make($cart));
    }


    /**
     * 삭제
     * @priority 1
     * @unauthenticated
     * @queryParam guest_id 비회원 아이디 Example: guestId
     */
    public function destroy(Request $request, $id)
    {
        Cart::mine($request)->findOrFail($id)->delete();
        return $this->respondSuccessfully();
    }

    /**
     * 선택 삭제
     * @unauthenticated
     * @queryParam guest_id 비회원 아이디 Example: guestId
     * @priority 1
     */
    public function destroys(Request $request)
    {
        $data = $request->validate(['ids' => ['required', 'array']]);
        Cart::mine($request)->whereIn('id', $data['ids'])->delete();
        return $this->respondSuccessfully();
    }

    /**
     * 품절 삭제
     * @unauthenticated
     * @queryParam guest_id 비회원 아이디 Example: guestId
     * @priority 1
     */
    public function destroySoldOut(Request $request)
    {
        Cart::mine($request)->whereHas('product', function ($query) {
            $query->where('stock_quantity', 0);
        })->delete();
        return $this->respondSuccessfully();
    }

}
