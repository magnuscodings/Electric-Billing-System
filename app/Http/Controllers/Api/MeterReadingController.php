<?php

namespace App\Http\Controllers\Api;

use App\Events\MeterReadingCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\MeterReadingRequests\StoreMeterReadingRequest;
use App\Models\Meter;
use App\Models\MeterReading;
use Illuminate\Support\Facades\Log;

class MeterReadingController extends Controller
{
    public function store(StoreMeterReadingRequest $request)
    {
        try {
            //Get previous reading
            $previousReading = MeterReading::where('meterId', $request->meterId)
                ->orderBy('id', 'desc')
                ->first();

            // Set up validation rules
            $rules = [
                'meterId' => 'required|exists:meters,id',
                'reading' => [
                    'required',
                    'numeric',
                    'min:0',
                    function ($attribute, $value, $fail) use ($previousReading) {
                        if ($previousReading && $value < $previousReading->reading) {
                            $fail("The new reading must be greater than the previous reading ({$previousReading->reading}).");
                        }
                    },
                ],
            ];

            // Validate the request
            $validated = $request->validate($rules);

            // Create the meter reading
            $meterReading = MeterReading::create($validated);

            // Count the number of meters without billing
            $newCount = Meter::with('latestReading')
                ->whereNotNull('clientId')
                ->whereHas('latestReading', function ($query) {
                    $query->whereDoesntHave('billing');
                })
                ->count();

            // Fire the event with the new count
            event(new MeterReadingCreated($meterReading, $newCount));

            return response()->json([
                'success' => true,
                'message' => 'Meter reading saved successfully',
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Meter Reading Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getLastReading($meterId)
    {
        try {
            $previousReading = MeterReading::where('meterId', $meterId)
                ->orderBy('id', 'desc')
                ->first();

            if (!$previousReading) {
                return response()->json([
                    'success' => true,
                    'message' => 'No previous reading found',
                    'data' => null
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Previous reading retrieved successfully',
                'data' => $previousReading
            ], 200);
        } catch (\Exception $e) {
            Log::error('Previous Reading Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve previous reading',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
