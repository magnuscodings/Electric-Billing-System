<?php

namespace App\Http\Controllers;

use App\Http\Requests\BillingRequests\StoreNewBillingRequest;
use App\Http\Resources\ApiResource\MeterResources\MeterListResource;
use App\Http\Resources\BillingResource\UnpaidBillingResource;
use App\Models\Billing;
use App\Models\Meter;
use App\Models\MeterReading;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Factory;

class BillingController extends Controller
{
    public function Billings()
    {
        $meters = Meter::with(['latestReading', 'previousReading'])
            ->whereNotNull('clientId')
            ->whereHas('latestReading', function ($query) {
                $query->whereDoesntHave('billing');
            })
            ->latest('created_at')
            ->paginate(10);

        // Get the latest rate from existing billings
        $currentRate = Billing::latest('created_at')->first()?->rate ?? 15.00;

        // Transform the data using the resource
        $meters = MeterListResource::collection($meters);

        return view('Billings', compact('meters', 'currentRate'));
    }

    public function getIncomingBillingRequestsCount()
    {
        $meters = Meter::with(['latestReading', 'previousReading'])
            ->whereNotNull('clientId')
            ->whereHas('latestReading', function ($query) {
                $query->whereDoesntHave('billing');
            })
            ->count();

        return response()->json([
            'count' => $meters
        ]);
    }


    public function getRowsBillingStatus($clientID)
    {
        // Ensure $clientID is valid before querying
        if (empty($clientID)) {
            return response()->json(['message' => 'Invalid client ID', 'data' => []], 400);
        }
    
        // Fetch all billing records for the given client ID(s)
        $meters = Billing::whereIn('clientId', (array) $clientID)->get();
    
        return response()->json(['count' => $meters->count(), 'data' => $meters]);
    }
    
    public function PendingBillings()
    {
        $unpaidBillings = Billing::with([
            'meterReading.meter.client',
            'meterReading.meter.previousReading'
        ])
            ->whereHas('meterReading.meter.client')
            ->unpaid()
            ->latest('billingDate')
            ->paginate(10);

        return view('Pendings', [
            'unpaidBillings' => UnpaidBillingResource::collection($unpaidBillings)
        ]);
    }

    public function store(StoreNewBillingRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Get the meter reading and calculate consumption
            $meterReading = MeterReading::with(['meter.previousReading', 'meter.client'])
                ->findOrFail($data['meterReadingId']);

            // Create the billing
            $billing = Billing::create([
                'meterReadingId' => $data['meterReadingId'],
                'clientId' => $meterReading->meter->client->id,
                'rate' => $data['rate'],
                'totalAmount' => $data['totalAmount'],
                'billingDate' => $data['billingDate'],
                'status' => Billing::STATUS_UNPAID,
                'notes' => $data['notes'] ?? null,
            ]);

            // Send Firebase Notification
            $this->sendPaymentConfirmationNotification($meterReading->meter->client, $billing);

            DB::commit();

            return redirect()->back()->with([
                'success' => 'Billing created successfully',
                'meterCode' => $meterReading->meter->meterCode
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error creating billing: ' . $e->getMessage()]);
        }
    }

//     public function generateBilling(StoreNewBillingRequest $request)
// {
//     // Fetch meters with readings
//     $meters = Meter::with('latestReading')->get();
    
//     foreach ($meters as $meter) {
//         // Ensure the meter has a valid reading
//         if (!$meter->latestReading) {
//             continue;
//         }

//         // Check if billing already exists for this meter reading
//         if (Billing::where('meter_reading_id', $meter->latestReading->id)->exists()) {
//             continue;
//         }

//         // Calculate consumption and total amount
//         $consumption = $meter->latestReading->consumption ?? 0;
//         $rate = Billing::latest('created_at')->first()?->rate ?? 15.00;
//         // $rate = Setting::where('key', 'rate_per_kwh')->value('value') ?? 0; // Fetch rate from settings
//         $totalAmount = $consumption * $rate;

//         // Save billing record
//         Billing::create([
//             'meter_reading_id' => $meter->latestReading->id,
//             'meter_code' => $meter->meterCode,
//             'current_reading' => $meter->latestReading->reading,
//             'previous_reading' => $meter->previousReading?->reading ?? 0,
//             'consumption' => $consumption,
//             'rate' => $rate,
//             'total_amount' => $totalAmount,
//             'billing_date' => now()->setDay(15)->format('Y-m-d'),
//         ]);
//     }

//     return response()->json(['message' => 'Billing generated successfully!'], 200);
// }


public function generate()
{
    // echo 123;
    // // Your logic for generating billing
    // return response()->json(['message' => 'Billing generated successfully']);

    $meters = Meter::with('latestReading')->get();
    
    foreach ($meters as $meter) {
        // Ensure the meter has a valid reading
        if (!$meter->latestReading) {
            continue;
        }

        // Check if billing already exists for this meter reading
        if (Billing::where('meterReadingId', $meter->latestReading->id)->exists()) {
            continue;
        }

        // Calculate consumption and total amount
        $consumption = $meter->latestReading->consumption ?? 0;
        $rate = Billing::latest('created_at')->first()?->rate ?? 15.00;
        // $rate = Setting::where('key', 'rate_per_kwh')->value('value') ?? 0; // Fetch rate from settings
        $totalAmount = $consumption * $rate;

        // Save billing record
        Billing::create([
            'meterReadingId' => $meter->latestReading->id,
            'clientId' => $meter->clientId,
            'rate' => $rate,
            'totalAmount' => $totalAmount,
            'billingDate' => now()->setDay(15)->format('Y-m-d'),
        ]);
    }

    return response()->json(['message' => 'Billing generated successfully!'], 200);
}



    public function markAsPaid($id)
    {
        try {
            DB::beginTransaction();

            $billing = Billing::findOrFail($id);

            // Only allow marking unpaid bills as paid
            if ($billing->status !== Billing::STATUS_UNPAID) {
                throw new \Exception('Only unpaid bills can be marked as paid.');
            }

            $billing->update([
                'status' => Billing::STATUS_PAID,
                'paymentDate' => now()
            ]);

            // Send notification to client about successful payment
            try {
                $this->sendPaymentConfirmationNotification($billing->client, $billing);
            } catch (\Exception $e) {
                // Log notification error but don't rollback transaction
                Log::error('Payment notification failed: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->back()->with('success', 'Bill marked as paid successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error marking bill as paid: ' . $e->getMessage()]);
        }
    }

    protected function sendPaymentConfirmationNotification($client, $billing)
    {
        // Skip if no FCM token
        if (!$client->fcm_token) {
            return;
        }

        $firebase = (new Factory())
            ->withServiceAccount(storage_path('app/firebase_credentials.json'));

        $messaging = $firebase->createMessaging();

        $message = CloudMessage::withTarget('token', $client->fcm_token)
            ->withNotification(Notification::create(
                'Payment Confirmed',
                "Your payment of ₱{$billing->totalAmount} has been recorded.",
                'notification_icon'
            ))
            ->withData([
                'type' => 'payment_confirmation',
                'billingId' => $billing->id,
                'amount' => $billing->totalAmount,
                'paymentDate' => $billing->paymentDate->format('Y-m-d')
            ]);

        $messaging->send($message);
    }

    protected function generateInvoicePdf(Billing $billing)
    {
        // Load necessary relationships
        $billing->load(['client', 'meterReading.meter', 'meterReading.meter.previousReading']);

        // Generate PDF
        $pdf = Pdf::loadView('invoicetemplate', [
            'billing' => $billing
        ]);

        // Generate filename
        $filename = 'Invoice-' . str_pad($billing->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($filename);
    }
}
