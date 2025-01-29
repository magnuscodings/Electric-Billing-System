@extends('layout.layout')

@section('content')
    <x-page-header homeUrl="{{ route('view.reports') }}" title="Monthly Report" />


    <div class="py-12">

        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-row-reverse mb-6">
                <a href="{{ route('view.report.monthly', ['month' => $month, 'print' => true]) }}" target="_blank"
                    class="px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-600">
                    Print Report
                </a>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">
                <div class="p-4 bg-white rounded-lg shadow">
                    <h3 class="text-lg font-semibold">Total Billings</h3>
                    <p class="text-2xl">₱{{ number_format($summary['total_amount'], 2) }}</p>
                    <p class="text-sm text-gray-500">{{ $summary['total_bills'] }} bills</p>
                </div>
                <div class="p-4 bg-white rounded-lg shadow">
                    <h3 class="text-lg font-semibold">Collections</h3>
                    <p class="text-2xl">₱{{ number_format($summary['paid_amount'], 2) }}</p>
                    <p class="text-sm text-gray-500">{{ number_format($summary['collection_rate'], 1) }}% collection rate
                    </p>
                </div>
                <div class="p-4 bg-white rounded-lg shadow">
                    <h3 class="text-lg font-semibold">Outstanding</h3>
                    <p class="text-2xl">₱{{ number_format($summary['unpaid_amount'] + $summary['overdue_amount'], 2) }}</p>
                    <p class="text-sm text-gray-500">Including overdue amounts</p>
                </div>
            </div>

            <!-- Billing List -->
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Client
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Meter Code
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Consumption
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Amount
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($report->flatten() as $billing)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            {{ $billing->client?->fullName ?? 'Unavailable' }}
                                            @if ($billing->client?->trashed())
                                                <span
                                                    class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full">
                                                    Deleted
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $billing->meterReading?->meter->meterCode ?? 'Unavailable' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $billing->meterReading?->consumption ?? 'N/A' }} kWh
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        ₱{{ number_format($billing->totalAmount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 py-1 text-sm {{ $billing->status === 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-full">
                                            {{ $billing->statusLabel }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
