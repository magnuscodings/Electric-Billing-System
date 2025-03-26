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
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomEmail;
use Illuminate\Http\Request;

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


    public function getBillingDueDate()
    {
        // $this->emailSend('dummy1stapador@gmail.com','Disconnection Notice','Your Bill has kwakwak');

        $days=6; //5Days from now for notice -1

        $today = Carbon::now()->toDateString(); 
        $threeDaysAhead = Carbon::now()->addDays($days)->toDateString(); 
    
        $unpaidBillings = Billing::with([
            'meterReading.meter.client',
            'meterReading.meter.previousReading'
        ])
            ->whereHas('meterReading.meter.client')
            ->unpaid()
            ->where('status', 0)
            ->get();
    
        $forDisconnection = [];
        $forNotice = [];
    
        foreach ($unpaidBillings as $billing) {
            $clientEmail = $billing->client->email;
            if ($billing->billingDate < $today) {

                if($billing->isnotifiedDisconnection==null || $billing->isnotifiedDisconnection=='')
                {
                // $forDisconnection[] = $billing;
                $forDisconnection[] = $clientEmail;
                $this->emailSend($clientEmail, 'Disconnection Notice', 'Your bill is overdue. Immediate payment is required.');

                $billing->isnotifiedDisconnection = 1;
                $billing->save(); // Save changes to the database
                }
            } elseif ($billing->billingDate >= $today && $billing->billingDate <= $threeDaysAhead) {
                if($billing->isnotifiedNotice==null || $billing->isnotifiedNotice=='')
                {
                    $forNotice[] = $clientEmail;
                    $this->emailSend($clientEmail, 'Payment Reminder', 'Your bill is due soon. Please pay before the due date.');
                    $billing->isnotifiedNotice = 1;
                    $billing->save(); // Save changes to the database
                }
              
            }
        }
      
        return [
            'forDisconnection' => $forDisconnection,
            'forNotice' => $forNotice
        ];
    }
    

    
    public function emailSend($email, $subject, $messageContent)
    {
        try {
            // Send the email
            Mail::to($email)->send(new CustomEmail($subject, $messageContent));
    
            // Check for failures
            if (count(Mail::failures()) > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email failed to send to ' . $email
                ], 500);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully to ' . $email
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email. ' . $e->getMessage()
            ], 500);
        }
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
            $this->saveToFirebase($meterReading->meter->client->id,$data['totalAmount'],"Pending");
            // Send Firebase Notification
            $this->sendPaymentConfirmationNotification($meterReading->meter->client, $billing,"");

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


public function generate()
{
    // echo 123;
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
        $totalAmount = $consumption * $rate;

        // Save billing record
        Billing::create([
            'meterReadingId' => $meter->latestReading->id,
            'clientId' => $meter->clientId,
            'rate' => $rate,
            'totalAmount' => $totalAmount,
            'billingDate' => now()->setDay(15)->format('Y-m-d'),
        ]);

        $this->saveToFirebase($meter->clientId,$totalAmount,"Pending");

    }

    return response()->json(['message' => 'Billing generated successfully!'], 200);
}


        public function markAsPaid(Request $request, $id)
    {
        $request->validate([
            'or_number' => 'required|string|max:255', // Ensure OR number is required
        ]);
        try {
            DB::beginTransaction();

            $billing = Billing::findOrFail($id);

            // Only allow marking unpaid bills as paid
            if ($billing->status !== Billing::STATUS_UNPAID) {
                throw new \Exception('Only unpaid bills can be marked as paid.');
            }

            $billing->update([
                'status' => Billing::STATUS_PAID,
                'paymentDate' => now(),
                'or_number' => $request->or_number, // Save OR number
                'isnotifiedNotice' => 0, 
                'isnotifiedDisconnection' => 0, 
            ]);
            // Send notification to client about successful payment
            try {
                $this->saveToFirebase($billing->clientId,  $billing->totalAmount, "Paid",$request->or_number);
                $this->sendPaymentConfirmationNotification($billing->client, $billing,$request->or_number);
            } catch (\Exception $e) {
                // Log notification error but don't rollback transaction
                Log::error('Payment notification failed: ' . $e->getMessage());
            }
            DB::commit();


            return redirect()->back()->with('success', 'Bill marked as paid successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error marking bill as paid: ' . $e->getMessage()]);
        }
    }


    protected function saveToFirebase($clientID, $amount, $status ,$ornumber = '')
    {
        // Initialize Firebase
        $firebase = (new Factory())
            ->withServiceAccount(storage_path('app/firebase_credentials.json'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL')); // Ensure your .env has FIREBASE_DATABASE_URL
    
        $database = $firebase->createDatabase();
    
        // Define billing data
        $data = [
            'clientId' => $clientID,
            'totalAmount' => $amount,
            'status' => $status,
            'ornumber' => $ornumber,
        ];
    
        // Generate a unique ID for each billing entry and push to the "billings" node
        $database->getReference('billings')->push($data);
    }
    
    
    
    protected function sendPaymentConfirmationNotification($client, $billing,$orNumber)
    {
        // Skip if no FCM token
        if (!$client->fcm_token) {
            return;
        }
        $body = "Your payment of ₱{$billing->totalAmount} has been recorded.";
        if($orNumber!='')
        {
            $body = "Your payment of ₱{$billing->totalAmount} has been recorded. OR #".$orNumber;
        }


        $firebase = (new Factory())
            ->withServiceAccount(storage_path('app/firebase_credentials.json'));

        $messaging = $firebase->createMessaging();

        $message = CloudMessage::withTarget('token', $client->fcm_token)
            ->withNotification(Notification::create(
                'Payment Confirmed',
                $body,
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
