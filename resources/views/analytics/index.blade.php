@extends('layouts.app')

@section('title', 'Personal Analytics')

@section('content')

<style>
    .analytics-page {
        max-width: 1400px;
        margin: 0 auto;
        color: #f8fafc;
    }

    /* HEADER */
    .analytics-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }

    .analytics-title {
        color: #ffffff !important;
        font-size: 1.75rem;
        font-weight: 750;
        margin: 0;
    }

    .analytics-subtitle {
        color: #cbd5e1 !important;
        font-size: .9rem;
        margin: 4px 0 0;
    }

    /* TOP STAT CARDS */
    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 14px;
    }

    .stat-card,
    .analytics-card {
        background: #151a21 !important;
        border: 1px solid #303844 !important;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, .2);
    }

    .stat-card {
        padding: 17px 18px;
        position: relative;
    }

    .stat-label {
        color: #cbd5e1 !important;
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .stat-value {
        color: #ffffff !important;
        font-size: 1.65rem;
        font-weight: 750;
        line-height: 1.15;
        margin-top: 5px;
    }

    .stat-note {
        color: #a8b4c3 !important;
        font-size: .75rem;
        margin-top: 5px;
    }

    .stat-icon {
        position: absolute;
        right: 16px;
        top: 16px;
        width: 39px;
        height: 39px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        background: rgba(34, 197, 94, .12);
    }

    /* MAIN ANALYTICS */
    .analytics-main {
        display: grid;
        grid-template-columns: 1fr 1.35fr;
        gap: 14px;
        margin-bottom: 14px;
    }

    .analytics-card {
        padding: 18px;
        min-height: 275px;
    }

    .card-heading {
        color: #ffffff !important;
        font-size: 1rem;
        font-weight: 750;
        margin-bottom: 3px;
    }

    .card-description {
        color: #a8b4c3 !important;
        font-size: .78rem;
        margin-bottom: 13px;
    }

    /* CHART */
    .chart-wrap {
        height: 190px;
        position: relative;
    }

    /* CATEGORY LIST */
    .category-list {
        margin-top: 5px;
    }

    .category-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 7px 0;
        border-bottom: 1px solid #303844;
        font-size: .8rem;
    }

    .category-row:last-child {
        border-bottom: 0;
    }

    .category-name {
        color: #e2e8f0 !important;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .category-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #22c55e;
    }

    .category-count {
        color: #ffffff !important;
        font-weight: 750;
    }

    /* INSIGHTS */
    .insight-box {
        background: rgba(34, 197, 94, .09) !important;
        border: 1px solid rgba(34, 197, 94, .18);
        border-radius: 12px;
        padding: 12px;
        margin-top: 13px;
    }

    .insight-label {
        color: #a8b4c3 !important;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .03em;
    }

    .insight-value {
        color: #ffffff !important;
        font-weight: 750;
        font-size: .94rem;
        margin-top: 3px;
    }

    /* BOTTOM */
    .wishlist-insights {
        min-height: 155px;
    }

    .wishlist-insight-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }

    .wishlist-insight-item {
        background: #1b212a;
        border: 1px solid #303844;
        border-radius: 12px;
        padding: 13px;
    }

    /* EMPTY */
    .empty-state {
        text-align: center;
        color: #a8b4c3 !important;
        padding: 35px 10px;
        font-size: .82rem;
    }

    /* PDF BUTTON */
    .pdf-btn {
        border: 0;
        border-radius: 10px;
        padding: 9px 14px;
        font-size: .8rem;
        font-weight: 700;
        color: #ffffff !important;
        background: #198754;
        text-decoration: none;
    }

    .pdf-btn:hover {
        color: #ffffff !important;
        background: #157347;
    }

    /* RESPONSIVE */
    @media (max-width: 1100px) {

        .analytics-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .analytics-main {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 768px) {

        .analytics-grid,
        .analytics-main,
        .wishlist-insight-grid {
            grid-template-columns: 1fr;
        }

        .analytics-header {
            align-items: flex-start;
            gap: 12px;
        }

        .analytics-title {
            font-size: 1.4rem;
        }
    }
</style>

<div class="analytics-page">

```
{{-- HEADER --}}
<div class="analytics-header">

    <div>
        <h1 class="analytics-title">
            Personal Analytics
        </h1>

        <p class="analytics-subtitle">
            Your BuildMyPC activity at a glance
        </p>
    </div>

        <a href="{{ route('analytics.pdf') }}" class="pdf-btn">
                ↓ Download PDF
        </a>

</div>


{{-- TOP STAT CARDS --}}
<div class="analytics-grid">

    {{-- WISHLIST --}}
    <div class="stat-card">

        <div class="stat-icon">❤️</div>

        <div class="stat-label">
            Wishlist
        </div>

        <div class="stat-value">
            {{ $wishlistCount }}
        </div>

        <div class="stat-note">
            Saved components
        </div>

    </div>


    {{-- ALERTS --}}
    <div class="stat-card">

        <div class="stat-icon">🔔</div>

        <div class="stat-label">
            Alerts
        </div>

        <div class="stat-value">
            {{ $alertCount }}
        </div>

        <div class="stat-note">
            {{ $unreadAlertCount }} unread
        </div>

    </div>


    {{-- COMPARISONS --}}
    <div class="stat-card">

        <div class="stat-icon">⚖️</div>

        <div class="stat-label">
            Comparisons
        </div>

        <div class="stat-value">
            {{ $comparisonCount }}
        </div>

        <div class="stat-note">
            Comparison sessions
        </div>

    </div>


    {{-- WISHLIST VALUE --}}
    <div class="stat-card">

        <div class="stat-icon">💰</div>

        <div class="stat-label">
            Wishlist Value
        </div>

        <div class="stat-value">
            ৳{{ number_format($wishlistValue, 0) }}
        </div>

        <div class="stat-note">
            Current product value
        </div>

    </div>

</div>


{{-- MAIN ANALYTICS --}}
<div class="analytics-main">

    {{-- COMPONENT INTERESTS --}}
    <div class="analytics-card">

        <div class="card-heading">
            Component Interests
        </div>

        <div class="card-description">
            Your wishlist by category
        </div>

        @if($wishlistByCategory->count())

            <div class="chart-wrap">
                <canvas id="wishlistChart"></canvas>
            </div>

            <div class="category-list">

                @foreach($wishlistByCategory->take(4) as $category => $count)

                    <div class="category-row">

                        <div class="category-name">

                            <span class="category-dot"></span>

                            {{ $category }}

                        </div>

                        <span class="category-count">
                            {{ $count }}
                        </span>

                    </div>

                @endforeach

            </div>

        @else

            <div class="empty-state">
                Add products to your wishlist to see your interests.
            </div>

        @endif

    </div>


    {{-- ACTIVITY OVERVIEW --}}
    <div class="analytics-card">

        <div class="card-heading">
            Activity Overview
        </div>

        <div class="card-description">
            Your activity across BuildMyPC
        </div>

        <div class="chart-wrap">
            <canvas id="activityChart"></canvas>
        </div>

        @if($mostComparedCategory)

            <div class="insight-box">

                <div class="insight-label">
                    MOST COMPARED CATEGORY
                </div>

                <div class="insight-value">
                    🏆 {{ $mostComparedCategory }}
                </div>

            </div>

        @endif

    </div>

</div>


{{-- WISHLIST INSIGHTS --}}
<div class="analytics-card wishlist-insights">

    <div class="card-heading">
        Wishlist Insights
    </div>

    <div class="card-description">
        A quick look at your saved hardware
    </div>

    <div class="wishlist-insight-grid">

        {{-- TOTAL VALUE --}}
        <div class="wishlist-insight-item">

            <div class="insight-label">
                TOTAL VALUE
            </div>

            <div class="insight-value">
                ৳{{ number_format($wishlistValue, 2) }}
            </div>

        </div>


        {{-- PRODUCTS SAVED --}}
        <div class="wishlist-insight-item">

            <div class="insight-label">
                PRODUCTS SAVED
            </div>

            <div class="insight-value">
                {{ $wishlistCount }}
            </div>

        </div>


        {{-- TOP CATEGORY --}}
        <div class="wishlist-insight-item">

            <div class="insight-label">
                TOP CATEGORY
            </div>

            <div class="insight-value">

                @if($wishlistByCategory->count())

                    {{ $wishlistByCategory->keys()->first() }}

                @else

                    None yet

                @endif

            </div>

        </div>

    </div>

</div>
```

</div>

{{-- CHART.JS --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

    /*
    |--------------------------------------------------------------------------
    | Wishlist Donut Chart
    |--------------------------------------------------------------------------
    */

    const wishlistLabels =
        @json($wishlistByCategory->keys()->values());

    const wishlistData =
        @json($wishlistByCategory->values());


    if (wishlistLabels.length) {

        new Chart(
            document.getElementById('wishlistChart'),
            {
                type: 'doughnut',

                data: {

                    labels: wishlistLabels,

                    datasets: [{

                        data: wishlistData,

                        borderWidth: 0,

                        hoverOffset: 6

                    }]
                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    cutout: '68%',

                    plugins: {

                        legend: {
                            display: false
                        }

                    }

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Activity Bar Chart
    |--------------------------------------------------------------------------
    */

    new Chart(
        document.getElementById('activityChart'),
        {
            type: 'bar',

            data: {

                labels: [
                    'Wishlist',
                    'Alerts',
                    'Comparisons'
                ],

                datasets: [{

                    data: [
                        {{ $wishlistCount }},
                        {{ $alertCount }},
                        {{ $comparisonCount }}
                    ],

                    borderRadius: 7,

                    borderWidth: 0

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {
                        display: false
                    }

                },

                scales: {

                    y: {

                        beginAtZero: true,

                        ticks: {
                            precision: 0,
                            color: '#cbd5e1'
                        },

                        grid: {
                            color: '#303844'
                        }

                    },

                    x: {

                        ticks: {

                            color: '#cbd5e1',

                            font: {
                                weight: '600'
                            }

                        },

                        grid: {
                            display: false
                        }

                    }

                }

            }

        }
    );

</script>

@endsection
