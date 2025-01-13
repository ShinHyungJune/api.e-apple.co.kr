<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PointResource;
use App\Models\Point;
use Illuminate\Http\Request;

class PointController extends Controller
{
    public function index(Request $request)
    {
        $filters = (array)json_decode($request->input('search'));
        $items = Point::with(['user'])->search($filters)->latest()->paginate($request->itemsPerPage ?? 30);
        return PointResource::collection($items);
    }
}
