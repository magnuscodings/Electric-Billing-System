<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Client;
use App\Models\Meter;
use App\Models\MeterReading;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get counts for cards
        $totalClients = Client::count();
        $totalRevenue = Billing::paid()->sum('totalAmount'); // Changed from meters to revenue
        $totalUnpaidBills = Billing::unpaid()->count();
        $totalOverdueBills = Billing::overdue()->count();

        // Get latest readings with client info
        $latestReadings = MeterReading::with(['meter.client'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get monthly consumption data for chart
        $monthlyConsumption = MeterReading::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(consumption) as total_consumption')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get()
            ->reverse();

        // Get payment status distribution
        $paymentStatus = Billing::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status_label => $item->count];
            });

        return view('dashboard', compact(
            'totalClients',
            'totalRevenue',  // Changed from totalMeters to totalRevenue
            'totalUnpaidBills',
            'totalOverdueBills',
            'latestReadings',
            'monthlyConsumption',
            'paymentStatus'
        ));
    }
}
