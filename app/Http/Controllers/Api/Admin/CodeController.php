<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exceptions\CustomException;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\CodeRequest;
use App\Http\Resources\CodeResource;
use App\Models\Code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CodeController extends ApiController
{

    public function index(Request $request, $parentId)
    {
        $codes = Code::getCodeItems();
        return response()->json($codes);
    }

    public function store(CodeRequest $request)
    {
        $data = $request->validated();
        $code = tap(new Code($data))->save();
        Code::rebuild(0, 0);

        $codes = Code::getCodeItems($code->id);
        //return response()->success($codes);
        return $this->respondSuccessfully(new CodeResource($codes));
    }

    public function update(CodeRequest $request, $id)
    {
        $data = $request->validated();
        $code = Code::findOrFail($id);
        $code = tap($code)->update($data);
        //Code::rebuild(0, 0);
        $codes = Code::getCodeItems($code->id);
        //return response()->success($codes);
        return $this->respondSuccessfully(new CodeResource($codes));
    }

    public function destroy($id)
    {
        if (Code::where('parent_id', $id)->count() > 0) {
            //throw new CustomException('등록된 하위카데고리가 있어 삭제할 수 없습니다.');
            abort(422, '등록된 하위카데고리가 있어 삭제할 수 없습니다.');
        }

        Code::destroy($id);
        //return response()->success();
        return $this->respondSuccessfully();
    }

    public function updateOrder(Request $request)
    {
        /*$data = $request->validated();
        DB::transaction(function () use ($data) {
            foreach ($data as $k => $v) { Code::findOrFail($v['value'])->update(['order' => $k]); }
        });*/
        $ids = implode(',', $request->ids);
        $query = "UPDATE codes JOIN (
                        SELECT
                            @rownum := @rownum + 1 AS `rownum`, id, `order`
                        FROM codes, (SELECT @rownum := 0) t
                        WHERE id IN (".$ids.") ORDER BY FIELD(id, ".$ids.")
                    ) t ON codes.id = t.id
                    SET codes.`order` = t.`rownum`";
        DB::update($query);
        //return response()->success();
        return $this->respondSuccessfully();
    }

}
