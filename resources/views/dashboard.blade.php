@extends('layout.layout')

@section('content')
    <x-page-header homeUrl="{{ route('view.dashboard') }}" title="Dashboard" />

    <div class="pt-6 px-4">
        <!-- Row 1: Stats Cards -->
        <div class="grid grid-cols-4 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <!-- Clients Card -->
            <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span class="text-2xl sm:text-3xl leading-none font-bold text-gray-900">{{ $totalClients }}</span>
                        <h3 class="text-base font-normal text-gray-500">Total Clients</h3>
                    </div>
                    <div class="ml-5 w-0 flex items-center justify-end flex-1 text-blue-500 text-base font-bold">
                        <x-lucide-users-round class="w-6 h-6" />
                    </div>
                </div>
            </div>
            <!-- Revenue Card (formerly Meters Card) -->
            <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span class="text-2xl sm:text-3xl leading-none font-bold text-gray-900">₱
                            {{ number_format($totalRevenue, 2) }}</span>
                        <h3 class="text-base font-normal text-gray-500">Total Revenue</h3>
                    </div>
                    <div class="ml-5 w-0 flex items-center justify-end flex-1 text-green-500 text-base font-bold">
                        <x-lucide-wallet class="w-6 h-6" />
                    </div>
                </div>
            </div>
            <!-- Unpaid Bills Card -->
            <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span
                            class="text-2xl sm:text-3xl leading-none font-bold text-gray-900">{{ $totalUnpaidBills }}</span>
                        <h3 class="text-base font-normal text-gray-500">Unpaid Bills</h3>
                    </div>
                    <div class="ml-5 w-0 flex items-center justify-end flex-1 text-yellow-500 text-base font-bold">
                        <x-lucide-circle-dollar-sign class="w-6 h-6" />
                    </div>
                </div>
            </div>
            <!-- Overdue Bills Card -->
            <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span
                            class="text-2xl sm:text-3xl leading-none font-bold text-gray-900">{{ $totalOverdueBills }}</span>
                        <h3 class="text-base font-normal text-gray-500">Overdue Bills</h3>
                    </div>
                    <div class="ml-5 w-0 flex items-center justify-end flex-1 text-red-500 text-base font-bold">
                        <x-lucide-clock-4 class="w-6 h-6" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
            <!-- Monthly Consumption Chart -->
            <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Monthly Consumption</h3>
                        <span class="text-base font-normal text-gray-500">Last 6 months consumption data</span>
                    </div>
                </div>
                <div class="h-80">
                    <div id="monthlyConsumptionChart"></div>
                </div>
            </div>

            <!-- Payment Status Distribution -->
            <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Payment Status Distribution</h3>
                        <span class="text-base font-normal text-gray-500">Current billing status overview</span>
                    </div>
                </div>
                <div class="h-80">
                    <div id="paymentStatusChart"></div>
                </div>
            </div>
        </div>

        <!-- Row 3: Latest Readings Table -->
        <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Latest Readings</h3>
                    <span class="text-base font-normal text-gray-500">Most recent meter readings</span>
                </div>
            </div>
            <div class="flex flex-col">
                <div class="overflow-x-auto">
                    <div class="align-middle inline-block min-w-full">
                        <div class="shadow overflow-hidden">
                            <table class="table-fixed min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th scope="col"
                                            class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Client
                                        </th>
                                        <th scope="col"
                                            class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Meter Code
                                        </th>
                                        <th scope="col"
                                            class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Reading
                                        </th>
                                        <th scope="col"
                                            class="p-4 text-left text-xs font-medium text-gray-500 uppercase">
                                            Consumption
                                        </th>
                                        <th scope="col"
                                            class="p-4 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($latestReadings as $reading)
                                        <tr class="hover:bg-gray-100">
                                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-900">
                                                {{ $reading->meter->client?->fullName }}
                                            </td>
                                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
                                                {{ $reading->meter->meterCode }}
                                            </td>
                                            <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                {{ number_format($reading->reading, 2) }}
                                            </td>
                                            <td class="p-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                {{ number_format($reading->consumption, 2) }}
                                            </td>
                                            <td class="p-4 whitespace-nowrap text-sm font-normal text-gray-500">
                                                {{ $reading->created_at->format('M d, Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Add ApexCharts Library --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.44.0/apexcharts.min.js"></script>

        <script>
            // Pass Laravel data to JavaScript
            const monthlyConsumptionData = @json(
                $monthlyConsumption->pluck('month')->map(function ($month) {
                    return \Carbon\Carbon::parse($month)->format('M Y');
                }));

            const monthlyConsumptionValues = @json($monthlyConsumption->pluck('total_consumption'));
            const paymentStatusLabels = @json(array_keys($paymentStatus->toArray()));
            const paymentStatusValues = @json(array_values($paymentStatus->toArray()));

            // Monthly Consumption Line Chart
            const monthlyConsumptionOptions = {
                chart: {
                    height: 300,
                    type: 'line',
                    fontFamily: 'Inter, sans-serif',
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                series: [{
                    name: 'Total Consumption',
                    data: monthlyConsumptionValues
                }],
                xaxis: {
                    categories: monthlyConsumptionData,
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '12px',
                            fontWeight: 400
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '12px',
                            fontWeight: 400
                        },
                        formatter: function(value) {
                            return value.toLocaleString();
                        }
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                colors: ['#3b82f6'],
                grid: {
                    show: true,
                    borderColor: '#e2e8f0',
                    strokeDashArray: 4,
                    padding: {
                        left: 2,
                        right: 2,
                        bottom: 0
                    }
                },
                tooltip: {
                    enabled: true,
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(value) {
                            return value.toLocaleString();
                        }
                    }
                },
                legend: {
                    show: false
                }
            };

            // Payment Status Distribution Donut Chart
            const paymentStatusOptions = {
                chart: {
                    height: 300,
                    type: 'donut',
                    fontFamily: 'Inter, sans-serif'
                },
                series: paymentStatusValues,
                labels: paymentStatusLabels,
                colors: [
                    '#3b82f6', // Blue
                    '#10b981', // Green
                    '#f59e0b', // Yellow
                    '#ef4444' // Red
                ],
                stroke: {
                    width: 0
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    fontSize: '16px',
                                    color: '#111827'
                                },
                                value: {
                                    fontSize: '16px',
                                    color: '#111827'
                                }
                            }
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    fontFamily: 'Inter, sans-serif',
                    fontSize: '14px',
                    markers: {
                        width: 12,
                        height: 12,
                        radius: 12
                    }
                },
                tooltip: {
                    enabled: true,
                    y: {
                        formatter: function(value) {
                            return value.toLocaleString();
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                }
            };

            // Wait for DOM to be ready
            document.addEventListener('DOMContentLoaded', function() {
                try {
                    // Initialize Monthly Consumption Chart
                    const monthlyConsumptionChart = new ApexCharts(
                        document.querySelector("#monthlyConsumptionChart"),
                        monthlyConsumptionOptions
                    );
                    monthlyConsumptionChart.render();

                    // Initialize Payment Status Chart
                    const paymentStatusChart = new ApexCharts(
                        document.querySelector("#paymentStatusChart"),
                        paymentStatusOptions
                    );
                    paymentStatusChart.render();
                } catch (error) {}
            });


            $(document).ready(function() {
                $.ajax({
                    url: "/getBillingDueDate", // Laravel route (update if needed)
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        console.log("Billing Due Data:", response);
                        // // Handle Notices
                        // if (response.forNotice.length > 0) {
                        //     alert("Notice emails sent to:\n" + response.forNotice.join("\n"));
                        // }

                        // // Handle Disconnections
                        // if (response.forDisconnection.length > 0) {
                        //     alert("Disconnection emails sent to:\n" + response.forDisconnection.join("\n"));
                        // }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", error);
                        alert("Failed to fetch billing data. Please try again.");
                    }
                });
            });
        </script>
    @endpush
@endsection
