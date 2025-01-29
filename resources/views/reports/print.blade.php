<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Report - {{ Carbon\Carbon::parse($month)->format('F Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .summary {
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
        }

        @media print {
            body {
                margin: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Monthly Billing Report</h1>
        <h2>{{ Carbon\Carbon::parse($month)->format('F Y') }}</h2>
    </div>

    <div class="summary">
        <h3>Summary</h3>
        <table>
            <tr>
                <td>Total Bills:</td>
                <td>{{ $summary['total_bills'] }}</td>
                <td>Total Amount:</td>
                <td>₱{{ number_format($summary['total_amount'], 2) }}</td>
            </tr>
            <tr>
                <td>Collected Amount:</td>
                <td>₱{{ number_format($summary['paid_amount'], 2) }}</td>
                <td>Collection Rate:</td>
                <td>{{ number_format($summary['collection_rate'], 1) }}%</td>
            </tr>
            <tr>
                <td>Outstanding Amount:</td>
                <td>₱{{ number_format($summary['unpaid_amount'], 2) }}</td>
                <td>Overdue Amount:</td>
                <td>₱{{ number_format($summary['overdue_amount'], 2) }}</td>
            </tr>
        </table>
    </div>

    <h3>Billing Details</h3>
    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Meter Code</th>
                <th>Consumption</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report->flatten() as $billing)
                <tr>
                    <td>
                        {{ $billing->client?->fullName ?? 'Unavailable' }}
                        @if ($billing->client?->trashed())
                            [Deleted]
                        @endif
                    </td>
                    <td>{{ $billing->meterReading?->meter->meterCode ?? 'Unavailable' }}</td>
                    <td>{{ $billing->meterReading?->consumption ?? 'N/A' }} kWh</td>
                    <td>₱{{ number_format($billing->totalAmount, 2) }}</td>
                    <td>{{ $billing->statusLabel }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>

</html>
