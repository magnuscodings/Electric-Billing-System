<?php

namespace App\Http\Controllers;

use App\Http\Requests\MeterRequests\StoreMeterRequest;
use App\Http\Resources\ApiResource\MeterResources\MeterListResource as MeterResourcesMeterListResource;
use App\Http\Resources\MeterResources\MeterListResource;
use App\Models\Meter;
use App\Models\MeterReading;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class MeterController extends Controller
{
    public function Meters()
    {
        $meters = MeterListResource::collection(Meter::with('latestReading')->paginate(10));
        return view('meters', compact('meters'));
    }

    // Update your search method to handle AJAX requests
    public function search(Request $request)
    {
        $query = $request->input('meterCode');

        $meters = Meter::where(function ($q) use ($query) {
            $q->where('meterCode', 'like', '%' . $query . '%');
        })->paginate(10);

        if ($request->wantsJson()) {
            return response()->json([
                'meters' => $meters
            ]);
        }

        return view('meters', compact('meters'));
    }

    public function validateMeterCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meterCode' => 'required|unique:meters,meterCode'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return response()->json([
            'success' => true
        ]);
    }

    public function Store(StoreMeterRequest $request)
    {
        try {
            // Check for duplicate meter code
            $existingMeter = Meter::where('meterCode', $request->meterCode)->first();

            if ($existingMeter) {
                return response()->json([
                    'success' => false,
                    'errors' => ['meterCode' => ['This meter code already exists.']]
                ], 422);
            }

            // // Wrap the entire logic inside a transaction
            // $meter = DB::transaction(function () use ($request) {
            //     // Create the meter
            //     $meter = Meter::create($request->validated());

            //     dd();
            //     // Create the meter reading and associate it with the meter
            //     MeterReading::create([
            //         'meterId' => $meter->id,
            //         'reading' => $request->input('reading'),
            //         'consumption' => $request->input('consumption'),
            //     ]);

            //     return $meter;
            // });




            $meter = DB::transaction(function () use ($request) {
                $meter = Meter::create([
                    'meterCode' => $request->meterCode,
                    'stallNumber' => $request->stallNumber, // Ensure this is included
                ]);
            
                MeterReading::create([
                    'meterId' => $meter->id,
                    'stallNumber' => $request->stallNumber, // Ensure this is included
                    'reading' => $request->input('reading'),
                    'consumption' => $request->input('consumption'),
                ]);
            
                return $meter;
            });
            

            return response()->json([
                'success' => true,
                'message' => 'Meter has been added successfully!',
                'meterCode' => $meter->meterCode
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['general' => [$e->getMessage()]]
            ], 500);
        }
    }

    public function show(string $id)
    {
        $meter = Meter::findOrFail($id);
        $readings = $meter->readings()
            ->with(['billing' => function ($query) {
                // Include soft deleted billings
                $query->withTrashed();
            }, 'billing.client' => function ($query) {
                $query->withTrashed();
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('meter.meter', compact('readings', 'meter'));
    }

    public function destroy(string $id)
    {
        try {
            $meter = DB::transaction(function () use ($id) {
                $meter = Meter::findOrFail($id);

                // Soft delete related readings first
                MeterReading::where('meterId', $id)->delete();

                // Then soft delete the meter
                $meter->delete();
            });
            return redirect()->back()->with([
                'success' => true,
                'message' => 'Meter deleted successfully',
                'meterCode' => $meter->meterCode
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(string $id)
    {
        $meter = Meter::with('latestReadings')->findOrFail($id);
        return response()->json($meter);
    }
}
