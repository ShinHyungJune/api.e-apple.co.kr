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
                      ->orWhere('orders.buyer_contact', 'like', '%' . $search['keyword'] . '%')
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

        // 선택된 ID들이 있으면 해당 항목만 필터링
        if ($selectedIds = $this->request->get('selectedIds')) {
            // 콤마로 구분된 문자열을 배열로 변환
            if (is_string($selectedIds)) {
                $selectedIds = explode(',', $selectedIds);
            }
            $query->whereIn('order_products.id', $selectedIds);
        }

        // 정렬
        $sortBy = $this->request->get('sortBy', 'created_at');
        // sortBy가 빈 문자열인 경우 기본값 설정
        if (empty($sortBy)) {
            $sortBy = 'created_at';
        }
        $sortDesc = $this->request->get('sortDesc', 'true') === 'true';
        $query->orderBy('order_products.' . $sortBy, $sortDesc ? 'desc' : 'asc');

        return $query;
    }

    public function headings(): array
    {
        return [
            '보내시는 분',
            '보내시는 분 전화',
            '보내는분총주소',
            '받으시는 분',
            '받으시는 분 전화',
            '받는분우편번호',
            '받는분총주소',
            '내품명1',
            '수량',
            '메모1',
            '내품주문번호1',
            '순번',
            '운임Type',
            '지불조건'
        ];
    }

    public function map($orderProduct): array
    {
        $order = $orderProduct->order;
        static $sequence = 0;
        $sequence++;

        return [
            '농업회사법인열매나무',  // 보내시는 분
            '055-945-3204',  // 보내시는 분 전화
            '경남 거창군 거창읍 거함대로 3372 서북부경남거점산지유통센터(APC)',  // 보내는분총주소
            $order->delivery_name ?? $order->buyer_name,  // 받으시는 분
            $order->delivery_phone ?? $order->buyer_contact ?? $order->user?->phone,  // 받으시는 분 전화
            $order->delivery_postal_code ?? $order->buyer_address_zipcode,  // 받는분우편번호
            ($order->delivery_address ?? $order->buyer_address) . ' ' . ($order->delivery_address_detail ?? $order->buyer_address_detail),  // 받는분총주소
            $orderProduct->product ? $orderProduct->product->name . ($orderProduct->productOption ? ' ' . $orderProduct->productOption->name : '') : '',  // 내품명1
            $orderProduct->quantity,  // 수량
            $order->delivery_request ?? $order->memo ?? '',  // 메모1
            $order->merchant_uid,  // 내품주문번호1
            $sequence,  // 순번
            '',  // 운임Type
            ''  // 지불조건
        ];
    }

    public function title(): string
    {
        return '한진택배_대량발송_' . date('Y-m-d');
    }
}