<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource\MeterResources\MeterListResource;
use App\Models\Meter;

class MeterController extends Controller
{
    public function index()
    {
        $meters = MeterListResource::collection(Meter::paginate(10));
        return response()->json($meters);
    }
}
