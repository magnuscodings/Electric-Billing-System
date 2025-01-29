<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Client Report - {{ $client->fullName }}</title>
    <style>
        /* Reuse your existing styles from report.blade.php */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 15px;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 14px;
            line-height: 24px;
        }

        .header {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .company-details {
            text-align: right;
            float: right;
        }

        .client-details {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background-color: #f8f8f8;
        }

        .section {
            margin-bottom: 30px;
        }

        .total {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header">
            <div class="company-details">
                <h2>EBS</h2>
                <p>Magdalena</p>
                <p>Phone: Phonehere</p>
            </div>
            <h1>CLIENT REPORT</h1>
            <div style="clear: both;"></div>
        </div>

        <div class="client-details">
            <h3>Client Information:</h3>
            <p>Name: {{ $client->fullName }}</p>
            <p>Address: {{ $client->address }}</p>
            <p>Stall Number: {{ $client->stallNumber }}</p>
            <p>Meter Number: {{ $client->meter->meterCode }}</p>
        </div>

        @if ($latestBilling)
            <div class="section">
                <h3>Latest Billing</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Billing Date</th>
                            <th>Previous Reading</th>
                            <th>Current Reading</th>
                            <th>Consumption</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $latestBilling->billingDate->format('M d, Y') }}</td>
                            <td>{{ number_format($latestBilling->meterReading->meter->previousReading?->reading, 0) ?? 0 }}
                            </td>
                            <td>{{ number_format($latestBilling->meterReading->reading, 0) }}</td>
                            <td>{{ number_format($latestBilling->meterReading->consumption, 0) }}</td>
                            <td>{{ number_format($latestBilling->rate, 0) }}</td>
                            <td>Php {{ number_format($latestBilling->totalAmount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        @if ($unpaidBillings->count() > 0)
            <div class="section">
                <h3>Unpaid Billings</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Billing Date</th>
                            <th>Due Date</th>
                            <th>Consumption</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($unpaidBillings as $billing)
                            <tr>
                                <td>{{ $billing->billingDate->format('M d, Y') }}</td>
                                <td>{{ $billing->billingDate->format('M d, Y') }}</td>
                                <td>{{ number_format($billing->meterReading->consumption, 0) }}</td>
                                <td>{{ number_format($billing->rate, 0) }}</td>
                                <td>Php {{ number_format($billing->totalAmount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="total">
                    <p>Total Unpaid Amount: Php {{ number_format($totalUnpaidAmount, 2) }}</p>
                </div>
            </div>
        @endif

        <div class="total">
            <p>Total Amount : Php {{ number_format($totalAmountToBePaid, 2) }}</p>
        </div>

        <div class="footer">
            <p>Report generated on {{ now()->format('M d, Y') }}</p>
        </div>

        <div class="no-print" style="margin-top: 20px; text-align: center;">
            <button onclick="window.print()">Print Report</button>
        </div>
    </div>
</body>

</html>
