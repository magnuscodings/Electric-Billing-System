<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource\MeterResources\MeterListResource;
use App\Models\Meter;
use App\Models\Billing;

class MeterController extends Controller
{
    // public function index()
    // {
    //     $meters = MeterListResource::collection(Meter::paginate(10));
    //     return response()->json($meters);
    // }

    public function index()
    {
        $meters = Meter::select('meters.*', 'c.count')
            ->leftJoinSub(
                Billing::selectRaw('COUNT(*) as count, clientId')
                    ->whereNull('paymentDate')
                    ->groupBy('clientId'),
                'c',
                'meters.clientId',
                '=',
                'c.clientId'
            )
            ->whereNull('c.count') // Filter where count IS NULL
            ->paginate(10); // Paginate results
    
        return response()->json(MeterListResource::collection($meters));
    }
    

}
