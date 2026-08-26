<!DOCTYPE html>

<html>
<head>
    <meta charset="UTF-8">

```
<title>BuildMyPC Personal Analytics</title>

<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        color: #1f2937;
        font-size: 12px;
        margin: 30px;
    }

    .header {
        text-align: center;
        margin-bottom: 25px;
    }

    .title {
        font-size: 24px;
        font-weight: bold;
        color: #111827;
    }

    .subtitle {
        color: #6b7280;
        margin-top: 5px;
    }

    .section {
        margin-top: 22px;
    }

    .section-title {
        font-size: 16px;
        font-weight: bold;
        color: #111827;
        border-bottom: 2px solid #198754;
        padding-bottom: 6px;
        margin-bottom: 12px;
    }

    .stats {
        width: 100%;
        border-collapse: collapse;
    }

    .stats td {
        width: 50%;
        padding: 8px;
        border: 1px solid #e5e7eb;
    }

    .label {
        color: #6b7280;
        font-size: 10px;
    }

    .value {
        font-size: 18px;
        font-weight: bold;
        margin-top: 3px;
    }

    table.data {
        width: 100%;
        border-collapse: collapse;
    }

    table.data th {
        background: #198754;
        color: white;
        padding: 8px;
        text-align: left;
    }

    table.data td {
        padding: 8px;
        border: 1px solid #e5e7eb;
    }

    .highlight {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        padding: 12px;
        margin-top: 12px;
    }

    .footer {
        margin-top: 35px;
        text-align: center;
        color: #9ca3af;
        font-size: 9px;
    }
</style>
```

</head>

<body>

```
<div class="header">

    <div class="title">
        BuildMyPC
    </div>

    <div style="font-size: 18px; margin-top: 8px;">
        Personal Analytics Report
    </div>

    <div class="subtitle">
        Generated for {{ $user->name }}
    </div>

    <div class="subtitle">
        {{ now()->format('d M Y, h:i A') }}
    </div>

</div>


{{-- SUMMARY --}}
<div class="section">

    <div class="section-title">
        Activity Summary
    </div>

    <table class="stats">

        <tr>

            <td>
                <div class="label">WISHLIST ITEMS</div>
                <div class="value">{{ $wishlistCount }}</div>
            </td>

            <td>
                <div class="label">WISHLIST VALUE</div>
                <div class="value">
                    ৳{{ number_format($wishlistValue, 2) }}
                </div>
            </td>

        </tr>

        <tr>

            <td>
                <div class="label">TOTAL ALERTS</div>
                <div class="value">{{ $alertCount }}</div>
            </td>

            <td>
                <div class="label">UNREAD ALERTS</div>
                <div class="value">{{ $unreadAlertCount }}</div>
            </td>

        </tr>

        <tr>

            <td>
                <div class="label">COMPARISONS</div>
                <div class="value">{{ $comparisonCount }}</div>
            </td>

            <td>
                <div class="label">MOST COMPARED CATEGORY</div>
                <div class="value" style="font-size: 14px;">
                    {{ $mostComparedCategory ?? 'None yet' }}
                </div>
            </td>

        </tr>

    </table>

</div>


{{-- WISHLIST --}}
<div class="section">

    <div class="section-title">
        Component Interests
    </div>

    @if($wishlistByCategory->count())

        <table class="data">

            <thead>
                <tr>
                    <th>Component Category</th>
                    <th>Items Saved</th>
                </tr>
            </thead>

            <tbody>

                @foreach($wishlistByCategory as $category => $count)

                    <tr>
                        <td>{{ $category }}</td>
                        <td>{{ $count }}</td>
                    </tr>

                @endforeach

            </tbody>

        </table>

    @else

        <div class="highlight">
            No wishlist items have been saved yet.
        </div>

    @endif

</div>


{{-- ALERTS --}}
<div class="section">

    <div class="section-title">
        Product Alerts
    </div>

    <table class="data">

        <thead>
            <tr>
                <th>Alert Type</th>
                <th>Count</th>
            </tr>
        </thead>

        <tbody>

            <tr>
                <td>Price Drops</td>
                <td>{{ $priceDropAlerts }}</td>
            </tr>

            <tr>
                <td>Back in Stock</td>
                <td>{{ $backInStockAlerts }}</td>
            </tr>

            <tr>
                <td>Price Drop + Back in Stock</td>
                <td>{{ $combinedAlerts }}</td>
            </tr>

            <tr>
                <td>Unread Alerts</td>
                <td>{{ $unreadAlertCount }}</td>
            </tr>

        </tbody>

    </table>

</div>


{{-- COMPARISONS --}}
<div class="section">

    <div class="section-title">
        Comparison Activity
    </div>

    @if($comparisonByCategory->count())

        <table class="data">

            <thead>
                <tr>
                    <th>Category</th>
                    <th>Comparisons</th>
                </tr>
            </thead>

            <tbody>

                @foreach($comparisonByCategory as $category => $count)

                    <tr>
                        <td>{{ $category }}</td>
                        <td>{{ $count }}</td>
                    </tr>

                @endforeach

            </tbody>

        </table>

    @else

        <div class="highlight">
            No comparison history available yet.
        </div>

    @endif

</div>


<div class="footer">
    BuildMyPC Personal Analytics • Generated automatically
</div>
```

</body>
</html>
