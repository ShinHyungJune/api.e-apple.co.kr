<?php

namespace App\Http\Middleware;

use App\Models\VisitorLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LogVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // API 요청이고 /api로 시작하는 경우만 기록
        if ($request->is('api/*') && !$request->is('api/admin/*')) {
            $ipAddress = $request->ip();
            $today = now()->toDateString();
            
            // 같은 IP에서 오늘 이미 방문했는지 확인
            $existingLog = VisitorLog::where('ip_address', $ipAddress)
                ->where('visit_date', $today)
                ->first();
            
            if (!$existingLog) {
                VisitorLog::create([
                    'ip_address' => $ipAddress,
                    'user_agent' => $request->userAgent(),
                    'path' => $request->path(),
                    'user_id' => Auth::check() ? Auth::id() : null,
                    'visit_date' => $today
                ]);
            }
        }
        
        return $next($request);
    }
}
