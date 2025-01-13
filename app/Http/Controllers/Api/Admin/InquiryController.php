<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\InquiryRequest;
use App\Http\Resources\InquiryResource;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class InquiryController extends ApiController
{
    public function index(Request $request)
    {
        $filters = (array)json_decode($request->input('search'));
        $items = Inquiry::with(['user'])->search($filters)->latest()->paginate($request->itemsPerPage ?? 30);
        return InquiryResource::collection($items);
    }

    public function store(InquiryRequest $request)
    {
        $data = $request->validated();
        $inquiry = tap(new Inquiry($data))->save();
        if ($request->file(Inquiry::IMAGES)) {
            foreach ($request->file(Inquiry::IMAGES) as $file) {
                $inquiry->addMedia($file)->toMediaCollection(Inquiry::IMAGES);
            }
        }
        return $this->respondSuccessfully(new InquiryResource($inquiry));
    }

    public function show(Request $request, Inquiry $inquiry)
    {
        $inquiry->load(['user']);
        return $this->respondSuccessfully(new InquiryResource($inquiry));
    }

    public function update(InquiryRequest $request, Inquiry $inquiry)
    {
        $inquiry->update($request->validated());
        if ($request->file(Inquiry::IMAGES)) {
            foreach ($request->file(Inquiry::IMAGES) as $file) {
                $inquiry->addMedia($file)->toMediaCollection(Inquiry::IMAGES);
            }
        }
        return $this->respondSuccessfully(new InquiryResource($inquiry));
    }

    public function destroy(Request $request, Inquiry $inquiry)
    {
        $inquiry->delete();
        $inquiry->clearMediaCollection(Inquiry::IMAGES);
        return $this->respondSuccessfully();
    }

    public function destroyImage(Media $media)
    {
        $media->delete();
        return $this->respondSuccessfully();
    }
}
