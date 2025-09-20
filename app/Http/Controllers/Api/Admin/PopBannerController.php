<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\PopBannerRequest;
use App\Http\Resources\PopBannerResource;
use App\Models\PopBanner;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PopBannerController extends ApiController
{
    public function index(Request $request)
    {
        $filters = (array)json_decode($request->input('search'));
        $items = PopBanner::search($filters)
            ->orderBy('sort_order', 'asc')
            ->latest()
            ->paginate($request->itemsPerPage ?? 30);
        return PopBannerResource::collection($items);
    }

    public function store(PopBannerRequest $request)
    {
        $data = $request->validated();
        $popBanner = tap(new PopBanner($data))->save();

        if ($request->hasFile('img')) {
            $popBanner->addMedia($request->file('img'))
                ->toMediaCollection(PopBanner::IMAGE);
        }

        return $this->respondSuccessfully(new PopBannerResource($popBanner));
    }

    public function show(Request $request, PopBanner $popBanner)
    {
        return $this->respondSuccessfully(new PopBannerResource($popBanner));
    }

    public function update(PopBannerRequest $request, PopBanner $popBanner)
    {
        $popBanner->update($request->validated());

        if ($request->hasFile('img')) {
            $popBanner->clearMediaCollection(PopBanner::IMAGE);
            $popBanner->addMedia($request->file('img'))
                ->toMediaCollection(PopBanner::IMAGE);
        }

        return $this->respondSuccessfully(new PopBannerResource($popBanner));
    }

    public function destroy(Request $request, PopBanner $popBanner)
    {
        $popBanner->delete();
        $popBanner->clearMediaCollection(PopBanner::IMAGE);
        return $this->respondSuccessfully();
    }

    public function destroyImage(Media $media)
    {
        $media->delete();
        return $this->respondSuccessfully();
    }
}