<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\BannerRequest;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BannerController extends ApiController
{

    public function index(Request $request)
    {
        $filters = (array)json_decode($request->input('search'));
        $items = Banner::search($filters)
            ->orderBy('sort_order', 'asc')
            ->latest()
            ->paginate($request->itemsPerPage ?? 30);
        return BannerResource::collection($items);
    }

    public function store(BannerRequest $request)
    {
        $data = $request->validated();
        $banner = tap(new Banner($data))->save();
        if ($request->file(Banner::IMAGES)) {
            foreach ($request->file(Banner::IMAGES) as $file) {
                $banner->addMedia($file)->toMediaCollection(Banner::IMAGES);
            }
        }
        return $this->respondSuccessfully(new BannerResource($banner));
    }

    public function show(Request $request, Banner $banner)
    {
        return $this->respondSuccessfully(new BannerResource($banner));
    }

    public function update(BannerRequest $request, Banner $banner)
    {
        $banner->update($request->validated());
        if ($request->file(Banner::IMAGES)) {
            foreach ($request->file(Banner::IMAGES) as $file) {
                $banner->addMedia($file)->toMediaCollection(Banner::IMAGES);
            }
        }
        return $this->respondSuccessfully(new BannerResource($banner));
    }

    public function destroy(Request $request, Banner $banner)
    {
        $banner->delete();
        $banner->clearMediaCollection(Banner::IMAGES);
        return $this->respondSuccessfully();
    }

    public function destroyImage(Media $media)
    {
        $media->delete();
        return $this->respondSuccessfully();
    }
}
