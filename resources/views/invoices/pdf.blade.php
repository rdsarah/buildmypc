<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 13px; color: #222; margin: 0; padding: 0; }
        .invoice-box { width: 100%; }
        .header-table { width: 100%; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
        .company-title { font-size: 24px; font-weight: bold; color: #1a1a1a; text-transform: uppercase; }
        .invoice-title { font-size: 20px; font-weight: bold; text-align: right; color: #555; }
        .details-table { width: 100%; margin-bottom: 20px; }
        .details-table td { vertical-align: top; width: 50%; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .items-table th { background-color: #2b2d42; color: #ffffff; padding: 10px; text-align: left; font-size: 12px; }
        .items-table td { border-bottom: 1px solid #ddd; padding: 10px; }
        .items-table tr:nth-child(even) { background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .total-section { margin-top: 20px; width: 100%; }
        .total-table { width: 40%; float: right; border-collapse: collapse; }
        .total-table td { padding: 8px; border-bottom: 1px solid #ccc; }
        .total-table .grand-total { font-weight: bold; font-size: 16px; border-top: 2px solid #2b2d42; border-bottom: none; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table class="header-table">
            <tr>
                <td>
                    <div class="company-title">BuildMyPC</div>
                    <small>Custom PC Builder & Hardware Store</small>
                </td>
                <td class="invoice-title">
                    INVOICE
                </td>
            </tr>
        </table>

        <table class="details-table">
            <tr>
                <td>
                    <strong>Billed To:</strong><br>
                    Name: {{ $order->customer_name ?? $order->user->name ?? 'Customer' }}<br>
                    Email: {{ $order->customer_email ?? $order->user->email ?? 'N/A' }}
                </td>
                <td class="text-right">
                    <strong>Invoice #:</strong> {{ $order->order_number }}<br>
                    <strong>Date:</strong> {{ $order->created_at ? $order->created_at->format('M d, Y') : date('M d, Y') }}<br>
                    <strong>Status:</strong> {{ ucfirst($order->status) }}
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? 'Component' }}</td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">BDT {{ number_format($item->price, 2) }}</td>
                        <td class="text-right">BDT {{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <table class="total-table">
                <tr class="grand-total">
                    <td>Total Paid:</td>
                    <td class="text-right">BDT {{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>