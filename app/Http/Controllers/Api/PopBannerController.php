<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PopBannerResource;
use App\Models\PopBanner;
use Illuminate\Http\Request;

class PopBannerController extends ApiController
{
    /**
     * 활성화된 팝업 배너 목록 조회
     */
    public function index(Request $request)
    {
        $popBanners = PopBanner::active()
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return PopBannerResource::collection($popBanners);
    }

    /**
     * 팝업 배너 상세 조회
     */
    public function show(Request $request, PopBanner $popBanner)
    {
        // 활성화된 배너만 조회 가능
        if (!$popBanner->is_active ||
            $popBanner->started_at > now() ||
            $popBanner->finished_at < now()) {
            abort(404);
        }

        return $this->respondSuccessfully(new PopBannerResource($popBanner));
    }
}