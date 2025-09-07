<?php

namespace App\Exports;

use App\Enums\DeliveryCompany;
use App\Enums\OrderStatus;
use App\Models\OrderProduct;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrderProductsExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    use Exportable;

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = OrderProduct::query()
            ->with(['order.user', 'product', 'productOption'])
            ->join('orders', 'order_products.order_id', '=', 'orders.id')
            ->select('order_products.*');

        // 검색 조건 적용
        if ($search = $this->request->get('search')) {
            $search = json_decode($search, true);
            
            if (!empty($search['keyword'])) {
                $query->where(function ($q) use ($search) {
                    $q->where('orders.merchant_uid', 'like', '%' . $search['keyword'] . '%')
                      ->orWhere('orders.buyer_name', 'like', '%' . $search['keyword'] . '%')
                      ->orWhere('orders.buyer_phone', 'like', '%' . $search['keyword'] . '%')
                      ->orWhere('order_products.delivery_tracking_number', 'like', '%' . $search['keyword'] . '%');
                });
            }

            if (!empty($search['status'])) {
                $query->where('order_products.status', $search['status']);
            }

            if (!empty($search['dates']) && count($search['dates']) == 2) {
                $query->whereBetween('order_products.created_at', [
                    $search['dates'][0] . ' 00:00:00',
                    $search['dates'][1] . ' 23:59:59'
                ]);
            }
        }

        // 정렬
        $sortBy = $this->request->get('sortBy', 'created_at');
        $sortDesc = $this->request->get('sortDesc', 'true') === 'true';
        $query->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');

        return $query;
    }

    public function headings(): array
    {
        return [
            '주문번호',
            '주문일시',
            '구매자명',
            '구매자연락처',
            '상품명',
            '옵션',
            '수량',
            '단가',
            '금액',
            '상태',
            '택배사',
            '송장번호',
            '배송비',
            '수령인',
            '수령인연락처',
            '배송주소',
            '배송메모',
            '출고일시'
        ];
    }

    public function map($orderProduct): array
    {
        $order = $orderProduct->order;
        
        return [
            $order->merchant_uid,
            $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : '',
            $order->buyer_name,
            $order->buyer_phone ?? $order->user?->phone,
            $orderProduct->product ? $orderProduct->product->name : '',
            $orderProduct->productOption ? $orderProduct->productOption->name : '',
            $orderProduct->quantity,
            number_format($orderProduct->price),
            number_format($orderProduct->price * $orderProduct->quantity),
            OrderStatus::from($orderProduct->status)->label(),
            $orderProduct->delivery_company ? DeliveryCompany::tryFrom($orderProduct->delivery_company)?->label() : '',
            $orderProduct->delivery_tracking_number,
            number_format($orderProduct->delivery_fee),
            $order->buyer_name,
            $order->buyer_phone ?? $order->user?->phone,
            $order->buyer_addr . ' ' . $order->buyer_addr_detail,
            $order->memo,
            $orderProduct->updated_at && $orderProduct->status === OrderStatus::DELIVERY ? $orderProduct->updated_at->format('Y-m-d H:i:s') : ''
        ];
    }

    public function title(): string
    {
        return '출고관리_' . date('Y-m-d');
    }
}