<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\InquiryRequest;
use App\Http\Resources\InquiryResource;
use App\Models\Inquiry;
use Illuminate\Http\Request;

/**
 * @group Inquiry(1:1문의)
 */
class InquiryController extends ApiController
{

    /**
     * 목록
     * @priority 1
     * @responseFile storage/responses/inquiries.json
     */
    public function index(Request $request)
    {
        $items = auth()->user()->inquiries()->latest()->paginate($request->get('take', 10));
        return InquiryResource::collection($items);
    }

    /**
     * 등록
     * @priority 1
     * @responseFile storage/responses/inquiry.json
     */
    public function store(InquiryRequest $request)
    {
        $data = $request->validated();
        $inquiry = auth()->user()->inquiries()->create($data);

        if ($request->file(Inquiry::IMAGES)) {
            foreach ($request->file(Inquiry::IMAGES) as $file) {
                $inquiry->addMedia($file)->toMediaCollection(Inquiry::IMAGES);
            }
        }

        return $this->respondSuccessfully(InquiryResource::make($inquiry));
    }

    /**
     * 삭제
     * @priority 1
     */
    public function destroy(Inquiry $inquiry)
    {
        if ($inquiry->user_id !== auth()->id()) abort(403);
        $inquiry->delete();
        return $this->respondSuccessfully();
    }

}
