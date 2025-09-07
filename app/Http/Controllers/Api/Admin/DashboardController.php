<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Api\ApiController;
use App\Models\Order;
use App\Models\VisitorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends ApiController
{
    /**
     * 대시보드 통계 데이터 조회
     */
    public function statistics(Request $request)
    {
        $startDate = now()->subDays(29)->startOfDay(); // 최근 30일
        $today = now()->toDateString();
        
        // 1. 최근 30일 일일 방문자수
        $dailyVisitors = VisitorLog::select(
                DB::raw('DATE(visit_date) as date'),
                DB::raw('COUNT(DISTINCT ip_address) as visitors')
            )
            ->where('visit_date', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'visitors' => $item->visitors
                ];
            });
        
        // 2. 최근 30일 일일 매출액
        $dailySales = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as sales')
            )
            ->whereIn('status', [
                OrderStatus::PAYMENT_COMPLETE->value,
                OrderStatus::DELIVERY_PREPARING->value,
                OrderStatus::DELIVERY->value,
                OrderStatus::DELIVERY_COMPLETE->value,
                OrderStatus::PURCHASE_CONFIRM->value
            ])
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'sales' => (int)$item->sales
                ];
            });
        
        // 3. 오늘의 주문건수
        $todayOrderCount = Order::whereDate('created_at', $today)
            ->whereNotIn('status', [
                OrderStatus::ORDER_PENDING->value,
                OrderStatus::PAYMENT_FAIL->value
            ])
            ->count();
        
        // 4. 오늘의 주문액
        $todayOrderAmount = Order::whereDate('created_at', $today)
            ->whereIn('status', [
                OrderStatus::PAYMENT_COMPLETE->value,
                OrderStatus::DELIVERY_PREPARING->value,
                OrderStatus::DELIVERY->value,
                OrderStatus::DELIVERY_COMPLETE->value,
                OrderStatus::PURCHASE_CONFIRM->value
            ])
            ->sum('total_amount');
        
        // 5. 전체 방문자수 (최근 30일)
        $totalVisitors = VisitorLog::where('visit_date', '>=', $startDate)
            ->distinct('ip_address')
            ->count('ip_address');
        
        // 6. 전체 매출액 (최근 30일)
        $totalSales = Order::whereIn('status', [
                OrderStatus::PAYMENT_COMPLETE->value,
                OrderStatus::DELIVERY_PREPARING->value,
                OrderStatus::DELIVERY->value,
                OrderStatus::DELIVERY_COMPLETE->value,
                OrderStatus::PURCHASE_CONFIRM->value
            ])
            ->where('created_at', '>=', $startDate)
            ->sum('total_amount');
        
        // 날짜 범위 생성 (데이터가 없는 날짜도 0으로 표시)
        $dateRange = [];
        for ($i = 29; $i >= 0; $i--) {
            $dateRange[] = now()->subDays($i)->format('Y-m-d');
        }
        
        // 방문자 데이터 채우기
        $visitorsByDate = $dailyVisitors->pluck('visitors', 'date')->toArray();
        $filledVisitors = [];
        foreach ($dateRange as $date) {
            $filledVisitors[] = [
                'date' => $date,
                'visitors' => $visitorsByDate[$date] ?? 0
            ];
        }
        
        // 매출 데이터 채우기
        $salesByDate = $dailySales->pluck('sales', 'date')->toArray();
        $filledSales = [];
        foreach ($dateRange as $date) {
            $filledSales[] = [
                'date' => $date,
                'sales' => $salesByDate[$date] ?? 0
            ];
        }
        
        return $this->respondSuccessfully([
            'daily_visitors' => $filledVisitors,
            'daily_sales' => $filledSales,
            'today_order_count' => $todayOrderCount,
            'today_order_amount' => (int)$todayOrderAmount,
            'total_visitors' => $totalVisitors,
            'total_sales' => (int)$totalSales,
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => now()->format('Y-m-d')
            ]
        ]);
    }
}
