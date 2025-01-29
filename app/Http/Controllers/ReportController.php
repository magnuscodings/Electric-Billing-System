<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $months = $this->getAvailableMonths();

        return view('reports.index', compact('months', 'currentMonth'));
    }

    public function monthlyReport(Request $request)
    {
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        $report = Billing::with([
            'client' => function ($query) {
                $query->withTrashed(); // Only keep withTrashed for clients
            },
            'meterReading.meter'
        ])
            ->whereBetween('billingDate', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($billing) {
                return $billing->status;
            });

        $summary = [
            'total_bills' => $report->flatten()->count(),
            'total_amount' => $report->flatten()->sum('totalAmount'),
            'paid_amount' => $report->get(Billing::STATUS_PAID, collect())->sum('totalAmount'),
            'unpaid_amount' => $report->get(Billing::STATUS_UNPAID, collect())->sum('totalAmount'),
            'overdue_amount' => $report->get(Billing::STATUS_OVERDUE, collect())->sum('totalAmount'),
            'collection_rate' => $report->flatten()->count() > 0
                ? ($report->get(Billing::STATUS_PAID, collect())->count() / $report->flatten()->count()) * 100
                : 0,
        ];

        $clientStats = Client::withTrashed()
            ->withCount([
                'billings' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('billingDate', [$startDate, $endDate]);
                },
                'billings as paid_billings' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('billingDate', [$startDate, $endDate])
                        ->where('status', Billing::STATUS_PAID);
                }
            ])->get();

        if ($request->get('print')) {
            return view('reports.print', compact('report', 'summary', 'month', 'clientStats'));
        }

        return view('reports.monthly', compact('report', 'summary', 'month', 'clientStats'));
    }

    private function getAvailableMonths()
    {
        return Billing::select(DB::raw('DATE_FORMAT(billingDate, "%Y-%m") as month'))
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month');
    }
}
