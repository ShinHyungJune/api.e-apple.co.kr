<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Code extends Model
{
    use HasFactory, SoftDeletes;

    const CACHE_SECONDS = -1;//IF NULL = FOREVER

    protected $guarded = ['id'];

    const PRODUCT_CATEGORY_ID = 1;

    const MONTHLY_SUGGESTION_CATEGORY_ID = 9;


    public static function rebuild($parentId, $leftId)
    {
        $rightId = $leftId + 1;
        $itemIds = self::select('id')->where('parent_id', $parentId)->orderBy('id', 'asc')->get();
        foreach ($itemIds as $item) {
            $rightId = self::rebuild($item->id, $rightId);
        }
        self::where('id', $parentId)->update(['left_id' => $leftId, 'right_id' => $rightId]);
        return $rightId + 1;
    }

    public static function getItems($id = 1, $isInfo = true)
    {
        $code = self::findOrFail($id);
        return self::getItemsData(self::codes($code), $code->parent_id, 0, $isInfo)[0]['items'];
    }

    private static function getItemsData($data, $parentId, $depth = 0, $isInfo = true)
    {
        $result = [];
        foreach ($data as $k => $v) {
            if ($v->parent_id === $parentId) {
                $items = ['value' => $v->id, 'text' => $v->name];
                $info = [];
                $children = [];
                if ($isInfo) {
                    //$info = ['info' => $v];
                    extract($v->toArray());
                    $info = ['info' => compact(['id', 'parent_id', 'name'])];
                }
                $countChild = ($v->right_id - $v->left_id - 1) / 2;
                if ($countChild > 0) {
                    $children = ['items' => self::getItemsData($data, $v->id, $depth + 1, $isInfo)];
                }

                $result[] = [...$items, ...$info, ...$children];
            }
        }
        return $result;
    }

    public function scopeSearch(Builder $query, $filters)
    {
        if (isset($filters['keyword'])) {
            $query->where('name', 'like', '%' . $filters['keyword'] . '%');
        }
    }
















    private static function codes($code)
    {
        //return Cache::remember('codes', self::CACHE_SECONDS, function () {
        return self::query()
            //->where('id', '!=', $code->id)
            ->where('is_use', true)->where('is_display', true)
            ->whereBetween('left_id', [$code->left_id, $code->right_id])
            //->orderBy('left_id', 'asc')
            ->orderBy('order', 'asc')
            ->get();
        //});
    }

    public static function getCodes($codeId)
    {
        //Cache::forget('codes_' . $codeId);
        //return Cache::rememberForever('codes_' . $codeId, function () use ($codeId)
        //return Cache::remember('codes_'.$codeId, self::CACHE_SECONDS, function () use ($codeId) {
        $code = Code::findOrFail($codeId);
        return Code::where('id', '!=', $code->id)
            ->whereBetween('left_id', [$code->left_id, $code->right_id])
            ->where('is_use', true)->where('is_display', true)
            ->orderBy('order', 'asc')->get();
        //});
    }

    public static function getIdValueCodes($codeId)
    {
        return self::getCodes($codeId)->pluck('name', 'id');
    }

    public static function getProductCategoryItems()
    {
        return self::getIdValueCodes(1);
    }

    public static function getCodeItems($id = 1, $isInfo = true)
    {
        $code = self::findOrFail($id);
        return self::getItemsData(self::codes($code), $code->parent_id, 0, $isInfo)[0];
    }

}
