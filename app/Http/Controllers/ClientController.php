<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Meter;

//Requests
use App\Http\Requests\ClientRequests\StoreClientRequest;

//Resources
use App\Http\Resources\ClientResources\ClientListResource;
use App\Http\Resources\ClientResources\StoreClientResource;
use App\Http\Resources\MeterResources\Dropdown_MeterResource;
use App\Models\Billing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    public function Clients()
    {
        $clients = ClientListResource::collection(Client::with('meter')->paginate(10));
        $meters = Dropdown_MeterResource::collection(Meter::orderBy('created_at', 'desc')->get()->all());
        return view('clients', compact('clients', 'meters'));
    }

    // Update your search method to handle AJAX requests
    public function search(Request $request)
    {
        $query = $request->input('email');

        $clients = Client::where(function ($q) use ($query) {
            $q->where('firstName', 'like', '%' . $query . '%')
                ->orWhere('lastName', 'like', '%' . $query . '%')
                ->orWhere('middleName', 'like', '%' . $query . '%')
                ->orWhere('suffix', 'like', '%' . $query . '%')
                ->orWhere('stallNumber', 'like', '%' . $query . '%')
                ->orWhere('email', 'like', '%' . $query . '%');
        })->with('meter')->paginate(10);

        $meters = Dropdown_MeterResource::collection(Meter::orderBy('created_at', 'desc')->get());

        if ($request->wantsJson()) {
            return response()->json([
                'clients' => ClientListResource::collection($clients),
                'meters' => $meters
            ]);
        }

        return view('clients', compact('clients', 'meters'));
    }

    public function show(string $id)
    {
        try {
            $client = Client::with('meter')->findOrFail($id);
            return response()->json($client);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch client: ' . $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $client = Client::findOrFail($id);

            // Validate the request
            $validated = $request->validate([
                'updateFirstName' => 'required|string|max:255',
                'updateMiddleName' => 'nullable|string|max:255',
                'updateLastName' => 'required|string|max:255',
                'updateSuffix' => 'nullable|string|max:255',
                'updateAddress' => 'required|string',
                'updateMeterCode' => 'required|exists:meters,id',
                'updateStallNumber' => 'required|string|max:255',
            ]);

            // Update client information
            $client->update([
                'firstName' => $validated['updateFirstName'],
                'middleName' => $validated['updateMiddleName'],
                'lastName' => $validated['updateLastName'],
                'suffix' => $validated['updateSuffix'],
                'address' => $validated['updateAddress'],
                'stallNumber' => $validated['updateStallNumber'],
            ]);

            // Handle meter code update
            if ($client->meter && $client->meter->id != $validated['updateMeterCode']) {
                // Clear the old meter's client ID
                $client->meter->update(['clientId' => null]);

                // Update the new meter with this client's ID
                Meter::findOrFail($validated['updateMeterCode'])->update(['clientId' => $client->id]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Client updated successfully!',
                'client' => new StoreClientResource($client)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update client: ' . $e->getMessage(),
                'errors' => $e instanceof ValidationException ? $e->errors() : null
            ], 422);
        }
    }

    public function Store(StoreClientRequest $request)
    {
        try {
            DB::beginTransaction();

            // Find meter code
            $meter = Meter::findOrFail($request->input('meterCode'));

            // Generate password using the pattern
            $password = $this->generatePasswordFromPattern(
                $request->input('firstName'),
                $meter->meterCode
            );

            // Create user account
            $user = User::create([
                'name' => $request->input('firstName') . ' ' . $request->input('lastName'),
                'email' => $request->input('email'),
                'password' => Hash::make($password),
                'role_id' => 3  // Client role ID based on RoleSeeder
            ]);

            // Create the client with user_id
            $clientData = $request->validated();
            $clientData['user_id'] = $user->id;
            $client = Client::create($clientData);

            //Update meter code if client was made
            $meter->clientId = $client->id;
            $meter->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Client added successfully!',
                'client' => new StoreClientResource($client),
                'login_credentials' => [
                    'email' => $user->email,
                    'password' => $password,
                    'pattern_explanation' => 'Password Pattern: FirstName@MeterCode'
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add client: ' . $e->getMessage(),
                'errors' => $e instanceof ValidationException ? $e->errors() : null
            ], 422);
        }
    }

    public function generatePasswordFromPattern($firstName, $meterCode)
    {
        // Convert to lowercase and remove any special characters
        $firstName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $firstName));

        // Capitalize first letter
        $firstName = ucfirst($firstName);

        // Add special character
        $specialChar = '@';

        return $firstName . $specialChar . $meterCode;
    }

    public function billings(string $id)
    {
        $client = Client::with(['billings' => function ($query) {
            $query->withTrashed() // Include soft-deleted billings
                ->orderBy('created_At', 'desc');
        }, 'billings.meterReading' => function ($query) {
            $query->withTrashed(); // Include soft-deleted meter readings
        }, 'billings.meterReading.meter' => function ($query) {
            $query->withTrashed(); // Include soft-deleted meters
        }])
            ->findOrFail($id);

        return view('clientBilling.clientBilling', compact('client'));
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $client = Client::findOrFail($id);

            // Clear the client ID from associated meter
            if ($client->meter) {
                $client->meter->update(['clientId' => null]);
            }

            // Delete the associated user account
            if ($client->user) {
                $client->user->delete();
            }

            // Soft delete the client
            $client->delete();

            DB::commit();

            return redirect()->route('view.clients')->with([
                'success' => ' has been deleted successfully!',
                'client' => new StoreClientResource($client),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete client. ' . $e->getMessage());
        }
    }

    public function printReport(Request $request)
    {
        try {
            $id = $request->id;

            // Find the client with their meter information
            $client = Client::with(['meter'])->findOrFail($id);

            // Get the latest billing
            $latestBilling = Billing::with(['meterReading.meter'])
                ->where('clientId', $id)
                ->latest('billingDate')
                ->first();

            // Get all unpaid billings
            $unpaidBillings = Billing::with(['meterReading.meter'])
                ->where('clientId', $id)
                ->where('status', 'unpaid')  // Assuming you have a status column
                ->orderBy('billingDate', 'desc')
                ->get();

            // Calculate total unpaid amount
            $totalUnpaidAmount = $unpaidBillings->sum('totalAmount');
            $totalAmountToBePaid = $unpaidBillings->sum('totalAmount') + $latestBilling->totalAmount;

            return view('clients.billing-report', compact(
                'client',
                'latestBilling',
                'unpaidBillings',
                'totalUnpaidAmount',
                'totalAmountToBePaid',
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate report. ' . $e->getMessage());
        }
    }
}
