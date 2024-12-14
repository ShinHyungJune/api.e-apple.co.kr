<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CartRequest;
use App\Http\Resources\CartResource;
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
     * @responseFile storage/responses/carts.json
     */
    public function index()
    {
        $items = auth()->user()->carts()->with(['product', 'cartProductOptions.productOption'])->paginate($request->take ?? 10);
        return CartResource::collection($items);
    }

    /**
     * 저장
     * @priority 1
     * @responseFile storage/responses/cart.json
     */
    public function store(CartRequest $request)
    {
        $data = $request->validated();
        //$product = Product::with(['options'])->findOrFail($data['product_id']);

        $cart = DB::transaction(function () use ($data) {
            $cart = auth()->user()->carts()->create(['product_id' => $data['product_id']])
                ->load('product.options');

            $cartProductOptions = [];
            foreach ($data['product_options'] as $productOption) {
                $option = $cart->product->options->findOrFail($productOption['product_option_id']);
                $cartProductOptions[] = [
                    'user_id' => auth()->id(),
                    'product_option_id' => $option->id,
                    'price' => $option->price,
                    'quantity' => $productOption['quantity'],
                ];
            }
            $cart->cartProductOptions()->createMany($cartProductOptions);

            return $cart->load('cartProductOptions.productOption');
        });

        return $this->respondSuccessfully(CartResource::make($cart));
    }

    /**
     * 삭제
     * @priority 1
     */
    public function destroy(Request $request, $id)
    {
        auth()->user()->carts()->findOrFail($id)->delete();
        return $this->respondSuccessfully();
    }

    /**
     * 선택 삭제
     * @priority 1
     */
    public function destroys(Request $request)
    {
        $data = $request->validate(['ids' => ['required', 'array']]);
        auth()->user()->carts()->whereIn('id', $data['ids'])->delete();
        return $this->respondSuccessfully();
    }

    /**
     * 품절 삭제
     * @priority 1
     */
    public function destroySoldOut(Request $request)
    {
        auth()->user()->carts()->with(['product'])->whereHas('product', function ($query) {
            $query->where('stock_quantity', 0);
        })->delete();
        return $this->respondSuccessfully();
    }


}
