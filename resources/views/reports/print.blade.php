<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportData['title'] }}</title>
    <style>
        body { font-family: sans-serif; color: #1e293b; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; color: #0284c7; }
        .header p { margin: 5px 0; color: #64748b; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        th, td { border: 1px solid #cbd5e1; padding: 10px; text-align: left; }
        th { background: #f1f5f9; color: #334155; }
        .total-row { font-weight: bold; background: #e0f2fe; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>Smart Personal Expense Tracker</h1>
        <h2>{{ $reportData['title'] }}</h2>
        <p>User: {{ $reportData['user']->name }} ({{ $reportData['user']->email }}) | Generated: {{ date('M d, Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Date</th>
                <th>Payment Method</th>
                <th>Amount ({{ $reportData['user']->currency_symbol }})</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['expenses'] as $exp)
            <tr>
                <td>{{ $exp->title }}</td>
                <td>{{ $exp->category ? $exp->category->name : 'Uncategorized' }}</td>
                <td>{{ $exp->date }}</td>
                <td>{{ $exp->payment_method }}</td>
                <td>{{ number_format($exp->amount, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">GRAND TOTAL:</td>
                <td>{{ number_format($reportData['total_amount'], 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
