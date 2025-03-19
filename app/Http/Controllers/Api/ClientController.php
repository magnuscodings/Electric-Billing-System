<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

//Resources
use App\Http\Resources\ClientResources\ClientListResource;

class ClientController extends Controller
{
    public function index()
    {
        return Cache::remember('clients.index', 30, function () {
            return ClientListResource::collection(Client::paginate(10));
        });
    }

    public function showMyClient()
    {
        try {
            $client = Client::with(['billings' => function ($query) {
                $query->withTrashed()
                    ->orderBy('created_at', 'desc');
            }, 'billings.meterReading' => function ($query) {
                $query->withTrashed();
            }, 'billings.meterReading.meter' => function ($query) {
                $query->withTrashed();
            }, 'meter', 'meter.readings' => function ($query) {
                $query->orderBy('created_at', 'desc')
                    ->take(6);
            }])
                ->where('user_id', auth()->id())
                ->firstOrFail();

            // $meterBalance = $client->billings()
            //     ->where('status', 0)
            //     ->sum('totalAmount');
            // $meterBalance = $client->billings()->sum('totalAmount');
            // dd($client->billings()->get());
            

            $meterBalance = DB::table('billings')
                ->where('clientId', $client->id) // Ensure it filters by the correct user
                ->where('status', 0)
                ->sum(DB::raw('COALESCE(totalAmount, 0)'));

            $consumptionData = $client->meter->readings
                ->map(function ($reading) {
                    return [
                        'month' => $reading->created_at->format('M Y'),
                        'consumption' => (float)$reading->consumption
                    ];
                })
                ->reverse()
                ->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'client' => [
                        'id' => $client->id,
                        'name' => $client->fullName,
                        'stallNumber' => $client->stallNumber,
                        'address' => $client->address,
                        'meter' => [
                            'id' => $client->meter->id,
                            'meterCode' => $client->meter->meterCode
                        ],
                        'meterBalance' => number_format($meterBalance, 2, '.', ''),
                        'consumptionData' => $consumptionData,
                        'billings' => $client->billings->map(function ($billing) {
                            return [
                                'meterCode' => $billing->meterReading->meter->meterCode ?? 'N/A',
                                'reading' => $billing->meterReading->reading ?? '0.00',
                                'dateOfReading' => isset($billing->meterReading->created_at) ?
                                    date('m-d-Y', strtotime($billing->meterReading->created_at)) : 'N/A',
                                'consumption' => $billing->meterReading->consumption ?? '0.00',
                                'rate' => $billing->rate ?? '0.00',
                                'totalAmount' => $billing->totalAmount ?? '0.00',
                                'billingDate' => date('m-d-Y', strtotime($billing->billingDate)),
                                'status' => $billing->status
                            ];
                        })
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch client details: ' . $e->getMessage()
            ], 500);
        }
    }
}
