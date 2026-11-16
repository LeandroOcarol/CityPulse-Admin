
@extends('admin.layouts.app')

@section('title', 'CityPulse | Predictive Analytics')
@section('body-class', 'predictive-analytics-page')

@push('styles')
<style>
    .predictive-analytics-page .content {
        padding: 24px;
    }

    .predictive-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 22px;
    }

    .predictive-header h1 {
        font-size: 26px;
        font-weight: 700;
        color: #192f22;
        margin: 0 0 6px;
    }

    .predictive-subtitle {
        font-size: 13px;
        color: #718075;
        line-height: 1.6;
    }

    .predictive-status {
        display: inline-flex;
        align-items: center;
        padding: 9px 14px;
        border-radius: 20px;
        background: #edf7f0;
        border: 1px solid #d0e7d7;
        color: #22663b;
        font-size: 12px;
        font-weight: 600;
    }

    .predictive-status.warning {
        background: #fff8e9;
        color: #8a621b;
        border-color: #f3dfb4;
    }

    .predictive-notice {
        background: #eef7f0;
        border: 1px solid #d0e6d5;
        border-radius: 10px;
        padding: 16px 18px;
        margin-bottom: 22px;
        font-size: 13px;
        line-height: 1.8;
        color: #2b5a3b;
    }

    .predictive-notice strong {
        font-weight: 700;
    }

    .predictive-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 22px;
    }

    .predictive-summary-card {
        background: #fff;
        border: 1px solid #e1e8e3;
        border-left: 4px solid #198b46;
        border-radius: 11px;
        padding: 22px 20px;
        min-height: 118px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 13px;
    }

    .predictive-summary-card.high {
        border-left-color: #d46c64;
    }

    .predictive-summary-card.medium {
        border-left-color: #e4b04d;
    }

    .predictive-summary-card.low {
        border-left-color: #239857;
    }

    .predictive-summary-card span {
        color: #66796b;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .predictive-summary-card strong {
        font-size: 30px;
        font-weight: 700;
        color: #178542;
    }

    .predictive-summary-card.high strong {
        color: #bc514a;
    }

    .predictive-summary-card.medium strong {
        color: #b98222;
    }

    .predictive-summary-card.low strong {
        color: #198b46;
    }

    .predictive-summary-card small {
        color: #819084;
        font-size: 11px;
    }

    .predictive-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .predictive-card {
        background: #fff;
        border: 1px solid #e1e8e3;
        border-radius: 12px;
        overflow: hidden;
        min-width: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,.025);
    }

    .predictive-full-width {
        grid-column: 1 / -1;
    }

    .predictive-card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #edf0ee;
    }

    .predictive-card-header h2 {
        font-size: 16px;
        color: #1b3024;
        margin: 0 0 5px;
        font-weight: 700;
    }

    .predictive-card-header span {
        font-size: 12px;
        color: #728075;
        line-height: 1.6;
    }

    .predictive-card-body {
        padding: 20px;
    }

    /* Risk Distribution */

    .predictive-risk-row {
        margin-bottom: 24px;
    }

    .predictive-risk-row:last-child {
        margin-bottom: 0;
    }

    .predictive-risk-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
        font-size: 13px;
    }

    .predictive-risk-heading strong {
        color: #253c2d;
    }

    .predictive-risk-heading span {
        color: #718075;
        font-size: 12px;
    }

    .predictive-risk-track {
        width: 100%;
        height: 14px;
        background: #edf1ee;
        border-radius: 20px;
        overflow: hidden;
    }

    .predictive-risk-fill {
        height: 100%;
        border-radius: 20px;
        transition: width .25s ease;
    }

    .predictive-risk-fill.high {
        background: #d46c64;
    }

    .predictive-risk-fill.medium {
        background: #e4b04d;
    }

    .predictive-risk-fill.low {
        background: #239857;
    }

    /* Overview */

    .predictive-info-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 0;
        border-bottom: 1px solid #edf0ee;
        font-size: 13px;
    }

    .predictive-info-row:last-child {
        border-bottom: none;
    }

    .predictive-info-row span {
        color: #748176;
    }

    .predictive-info-row strong {
        color: #285438;
        text-align: right;
    }

    .predictive-description {
        margin: 15px 0 0;
        color: #748176;
        font-size: 12px;
        line-height: 1.8;
    }

    /* Barangay Search and Filter */

    .predictive-toolbar {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .predictive-search,
    .predictive-filter {
        background: #fff;
        border: 1px solid #dce7df;
        border-radius: 8px;
        padding: 11px 13px;
        font-size: 13px;
        color: #284a35;
        outline: none;
    }

    .predictive-search {
        flex: 1;
        min-width: 220px;
    }

    .predictive-filter {
        min-width: 170px;
    }

    .predictive-search:focus,
    .predictive-filter:focus {
        border-color: #198b46;
        box-shadow: 0 0 0 3px rgba(25,139,70,.08);
    }

    /* Scrollable Barangay Prediction Table */

    .predictive-table-wrapper {
        width: 100%;
        max-height: 420px;
        overflow-y: auto;
        overflow-x: auto;
        border: 1px solid #e2ebe5;
        border-radius: 9px;
        scrollbar-width: thin;
        scrollbar-color: #a7c9b2 #f2f6f3;
        position: relative;
    }

    .predictive-table-wrapper::-webkit-scrollbar {
        width: 9px;
        height: 9px;
    }

    .predictive-table-wrapper::-webkit-scrollbar-track {
        background: #f2f6f3;
        border-radius: 8px;
    }

    .predictive-table-wrapper::-webkit-scrollbar-thumb {
        background: #a7c9b2;
        border-radius: 8px;
        border: 2px solid #f2f6f3;
    }

    .predictive-table-wrapper::-webkit-scrollbar-thumb:hover {
        background: #6aa980;
    }

    .predictive-table {
        width: 100%;
        min-width: 850px;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 12px;
        text-align: left;
    }

    .predictive-table thead {
        position: sticky;
        top: 0;
        z-index: 5;
    }

    .predictive-table th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #f3f8f5;
        color: #315a3e;
        padding: 13px 12px;
        border-bottom: 1px solid #dce9df;
        white-space: nowrap;
        font-weight: 700;
    }

    .predictive-table td {
        padding: 14px 12px;
        border-bottom: 1px solid #edf0ee;
        color: #385043;
        white-space: nowrap;
        background: #fff;
    }

    .predictive-table tbody tr:last-child td {
        border-bottom: none;
    }

    .predictive-table tbody tr:hover td {
        background: #fafcfb;
    }

    /* Risk Badges */

    .predictive-risk-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .predictive-risk-badge.high {
        background: #fcebea;
        color: #ac4640;
    }

    .predictive-risk-badge.medium {
        background: #fff4dc;
        color: #99650d;
    }

    .predictive-risk-badge.low {
        background: #e7f6ec;
        color: #197943;
    }

    .predictive-directory-summary {
        font-size: 12px;
        color: #728075;
        margin-top: 16px;
    }

    .predictive-scroll-hint {
        margin: 0 0 10px;
        color: #718075;
        font-size: 11px;
    }

    .predictive-empty {
        padding: 25px;
        text-align: center;
        font-size: 13px;
        color: #758378;
    }

    .predictive-empty[hidden] {
        display: none !important;
    }

    .predictive-placeholder {
        min-height: 160px;
        border: 1px dashed #d6e5da;
        border-radius: 10px;
        background: #fafcfb;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 10px;
        color: #708075;
        font-size: 12px;
        padding: 20px;
        line-height: 1.7;
    }

    .predictive-placeholder strong {
        color: #2d593c;
        font-size: 14px;
    }

    /* Safety Routing Explanation */

    .predictive-routing-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .predictive-routing-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .predictive-routing-number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        min-width: 28px;
        border-radius: 50%;
        background: #e7f4ea;
        color: #198b46;
        font-size: 12px;
        font-weight: 700;
    }

    .predictive-routing-text strong {
        display: block;
        color: #285438;
        font-size: 13px;
        margin-bottom: 4px;
    }

    .predictive-routing-text p {
        margin: 0;
        color: #748176;
        font-size: 12px;
        line-height: 1.7;
    }

    @media (max-width: 1100px) {
        .predictive-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 720px) {
        .predictive-analytics-page .content {
            padding: 14px;
        }

        .predictive-summary,
        .predictive-grid {
            grid-template-columns: 1fr;
        }

        .predictive-full-width {
            grid-column: auto;
        }

        .predictive-header h1 {
            font-size: 22px;
        }

        .predictive-search,
        .predictive-filter {
            width: 100%;
        }

        .predictive-summary-card {
            min-height: 105px;
        }

        .predictive-table-wrapper {
            max-height: 380px;
        }
    }
</style>
@endpush

@section('content')

@php
    $hasPredictions =
        ($fileStatus['predictions'] ?? false)
        && ($predictedBarangays ?? 0) > 0;

    $totalBarangays = $hasPredictions
        ? $predictedBarangays
        : 0;

    $highRisk = $hasPredictions
        ? ($riskDistribution['High'] ?? 0)
        : 0;

    $mediumRisk = $hasPredictions
        ? ($riskDistribution['Medium'] ?? 0)
        : 0;

    $lowRisk = $hasPredictions
        ? ($riskDistribution['Low'] ?? 0)
        : 0;

    $riskLevels = ['High', 'Medium', 'Low'];

    $displayMonth = $hasPredictions
        ? ($predictionMonth ?? '2026-07')
        : null;

    $formattedMonth = $displayMonth
        ? \Carbon\Carbon::createFromFormat(
            '!Y-m',
            $displayMonth
        )->format('F Y')
        : 'Unavailable';
@endphp

<!-- Page Header -->

<div class="predictive-header">

    <div>
        <h1>Predictive Analytics</h1>

        <div class="predictive-subtitle">
            Geographic safety risk predictions
            supporting CityPulse tourism monitoring
        </div>
    </div>

    @if($hasPredictions)
        <span class="predictive-status">
            Historical Predictions Loaded
        </span>
    @else
        <span class="predictive-status warning">
            Prediction Data Unavailable
        </span>
    @endif

</div>

<!-- Historical Notice -->

<div class="predictive-notice">

    <strong>CityPulse Predictive Safety Analytics</strong>

    <p>
        This dashboard presents barangay-level risk
        classifications generated by the CityPulse
        Random Forest Classifier using historical
        crime information.
    </p>

    @if($hasPredictions)
        <p>
            Prediction period:
            <strong>{{ $formattedMonth }}</strong>.
        </p>
    @endif

    <p>
        These results are historical predictions,
        not live safety conditions.
        Android safety-aware routing integration
        has not yet been verified.
    </p>

</div>

<!-- Risk Summary -->

<section class="predictive-summary">

    <div class="predictive-summary-card">

        <span>Total Classified Barangays</span>

        <strong>
            {{ $hasPredictions
                ? number_format($totalBarangays)
                : 'N/A' }}
        </strong>

        <small>Historical prediction records</small>

    </div>

    <div class="predictive-summary-card high">

        <span>High Risk Barangays</span>

        <strong>
            {{ $hasPredictions
                ? number_format($highRisk)
                : 'N/A' }}
        </strong>

        <small>Elevated predicted geographic risk</small>

    </div>

    <div class="predictive-summary-card medium">

        <span>Medium Risk Barangays</span>

        <strong>
            {{ $hasPredictions
                ? number_format($mediumRisk)
                : 'N/A' }}
        </strong>

        <small>Moderate predicted geographic risk</small>

    </div>

    <div class="predictive-summary-card low">

        <span>Low Risk Barangays</span>

        <strong>
            {{ $hasPredictions
                ? number_format($lowRisk)
                : 'N/A' }}
        </strong>

        <small>Lower predicted geographic risk</small>

    </div>

</section>

<div class="predictive-grid">

    <!-- Geographic Risk Distribution -->

    <section class="predictive-card">

        <div class="predictive-card-header">

            <h2>Geographic Risk Distribution</h2>

            <span>
                Distribution of predicted risk
                levels for {{ $formattedMonth }}
            </span>

        </div>

        <div class="predictive-card-body">

            @if($hasPredictions)

                @foreach($riskLevels as $level)

                    @php
                        $count = $riskDistribution[$level] ?? 0;
                        $percentage =
                            $riskPercentages[$level] ?? 0;
                    @endphp

                    <div class="predictive-risk-row">

                        <div class="predictive-risk-heading">

                            <strong>{{ $level }} Risk</strong>

                            <span>
                                {{ $count }} barangays
                                ({{ number_format($percentage, 2) }}%)
                            </span>

                        </div>

                        <div class="predictive-risk-track">

                            <div
                                class="predictive-risk-fill {{ strtolower($level) }}"
                                style="width: {{ max(0, min(100, $percentage)) }}%;"
                            ></div>

                        </div>

                    </div>

                @endforeach

                <p class="predictive-description">
                    These classifications represent
                    predicted monthly geographic risk,
                    not confirmed future incidents.
                </p>

            @else

                <div class="predictive-placeholder">

                    <strong>Risk Distribution Unavailable</strong>

                    The historical prediction CSV
                    could not be loaded.

                </div>

            @endif

        </div>

    </section>

    <!-- Prediction Overview -->

    <section class="predictive-card">

        <div class="predictive-card-header">

            <h2>Prediction Overview</h2>

            <span>
                Summary of available geographic
                risk classification data
            </span>

        </div>

        <div class="predictive-card-body">

            <div class="predictive-info-row">
                <span>Prediction Period</span>
                <strong>{{ $formattedMonth }}</strong>
            </div>

            <div class="predictive-info-row">
                <span>Geographic Coverage</span>
                <strong>Cebu City Barangays</strong>
            </div>

            <div class="predictive-info-row">
                <span>Classification Method</span>
                <strong>Random Forest Classifier</strong>
            </div>

            <div class="predictive-info-row">
                <span>Prediction Categories</span>
                <strong>Low, Medium, High</strong>
            </div>

            <div class="predictive-info-row">
                <span>Total Prediction Records</span>
                <strong>
                    {{ $hasPredictions
                        ? number_format($totalBarangays)
                        : 'N/A' }}
                </strong>
            </div>

            <div class="predictive-info-row">
                <span>Prediction Source</span>
                <strong>Historical Crime Statistics</strong>
            </div>

            <div class="predictive-info-row">
                <span>Prediction Availability</span>
                <strong>Historical Export Only</strong>
            </div>

            <p class="predictive-description">
                Predictions are classified at the
                barangay level. They do not directly
                represent individual road segments,
                tourist spots, or live route conditions.
            </p>

        </div>

    </section>

    <!-- Barangay Risk Prediction Directory -->

    <section class="predictive-card predictive-full-width">

        <div class="predictive-card-header">

            <h2>Barangay Risk Predictions</h2>

            <span>
                Search and review historical
                geographic risk classifications
                for {{ $formattedMonth }}
            </span>

        </div>

        <div class="predictive-card-body">

            @if($hasPredictions)

                <!-- Search and Filter -->

                <div class="predictive-toolbar">

                    <input
                        type="search"
                        id="predictiveBarangaySearch"
                        class="predictive-search"
                        placeholder="Search barangay..."
                        aria-label="Search barangay"
                    >

                    <select
                        id="predictiveRiskFilter"
                        class="predictive-filter"
                        aria-label="Filter by risk level"
                    >

                        <option value="all">
                            All Risk Levels
                        </option>

                        <option value="High">
                            High Risk
                        </option>

                        <option value="Medium">
                            Medium Risk
                        </option>

                        <option value="Low">
                            Low Risk
                        </option>

                    </select>

                </div>

                <p class="predictive-scroll-hint">
                    Scroll inside the table to view
                    all barangay prediction records.
                </p>

                <!-- Scrollable Table -->

                <div
                    class="predictive-table-wrapper"
                    id="predictiveTableScroll"
                    tabindex="0"
                    role="region"
                    aria-label="Scrollable barangay risk predictions"
                >

                    <table
                        class="predictive-table"
                        id="predictiveBarangayTable"
                    >

                        <thead>
                            <tr>
                                <th>Barangay</th>
                                <th>Predicted Risk</th>
                                <th>Confidence</th>
                                <th>Historical Crimes</th>
                                <th>Weighted Crime Score</th>
                                <th>3-Month Crime Total</th>
                                <th>Prediction Period</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($predictions as $prediction)

                                <tr
                                    class="predictive-barangay-row"
                                    data-barangay="{{ strtolower($prediction['barangay']) }}"
                                    data-risk="{{ $prediction['risk_level'] }}"
                                >

                                    <td>
                                        <strong>
                                            {{ $prediction['barangay'] }}
                                        </strong>
                                    </td>

                                    <td>

                                        <span
                                            class="predictive-risk-badge {{ strtolower($prediction['risk_level']) }}"
                                        >
                                            {{ $prediction['risk_level'] }}
                                        </span>

                                    </td>

                                    <td>
                                        {{ $prediction['confidence_percentage'] !== null
                                            ? number_format($prediction['confidence_percentage'], 2) . '%'
                                            : 'N/A' }}
                                    </td>

                                    <td>
                                        {{ number_format(
                                            $prediction['current_total_crimes']
                                        ) }}
                                    </td>

                                    <td>
                                        {{ $prediction['current_weighted_score'] !== null
                                            ? number_format($prediction['current_weighted_score'], 0)
                                            : 'N/A' }}
                                    </td>

                                    <td>
                                        {{ $prediction['rolling_3m_total_crimes'] !== null
                                            ? number_format($prediction['rolling_3m_total_crimes'], 0)
                                            : 'N/A' }}
                                    </td>

                                    <td>
                                        {{ $prediction['prediction_for_month'] }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                <!-- Empty Filter Result -->

                <div
                    class="predictive-empty"
                    id="predictiveEmptyResult"
                    hidden
                >
                    No barangays match your search
                    or selected risk level.
                </div>

                <!-- Results Counter -->

                <div class="predictive-directory-summary">

                    Showing

                    <strong id="predictiveVisibleCount">
                        {{ $totalBarangays }}
                    </strong>

                    of

                    <strong>
                        {{ $totalBarangays }}
                    </strong>

                    barangay predictions.

                </div>

                <p class="predictive-description">

                    Confidence represents the
                    Random Forest model's estimated
                    probability for the predicted class.
                    It does not guarantee correctness.

                    Historical crime totals and
                    weighted scores are input
                    indicators, not predicted
                    future crime counts.

                </p>

            @else

                <div class="predictive-placeholder">

                    <strong>
                        Barangay Predictions Unavailable
                    </strong>

                    Verify the July 2026 prediction
                    CSV in Laravel private storage.

                </div>

            @endif

        </div>

    </section>

    <!-- Safety-Aware Routing Context -->

    <section class="predictive-card predictive-full-width">

        <div class="predictive-card-header">

            <h2>
                Predictive Analytics and
                Safety-Aware Routing
            </h2>

            <span>
                How geographic risk classifications
                are intended to support tourist navigation
            </span>

        </div>

        <div class="predictive-card-body">

            <div class="predictive-routing-list">

                <div class="predictive-routing-item">

                    <div class="predictive-routing-number">
                        1
                    </div>

                    <div class="predictive-routing-text">

                        <strong>
                            Geographic Risk Classification
                        </strong>

                        <p>
                            The Random Forest Classifier
                            estimates Low, Medium, or High
                            Risk classifications using
                            historical crime statistics.
                        </p>

                    </div>

                </div>

                <div class="predictive-routing-item">

                    <div class="predictive-routing-number">
                        2
                    </div>

                    <div class="predictive-routing-text">

                        <strong>
                            Safety Data Integration
                        </strong>

                        <p>
                            Once integrated, predicted
                            geographic risks can be
                            combined with geofence data
                            and other safety indicators.
                        </p>

                    </div>

                </div>

                <div class="predictive-routing-item">

                    <div class="predictive-routing-number">
                        3
                    </div>

                    <div class="predictive-routing-text">

                        <strong>
                            Safety-Aware Route Evaluation
                        </strong>

                        <p>
                            The Tourist Android application
                            is intended to evaluate route
                            alternatives using risk,
                            travel distance, and proximity
                            to police stations or outposts.
                        </p>

                    </div>

                </div>

                <div class="predictive-routing-item">

                    <div class="predictive-routing-number">
                        4
                    </div>

                    <div class="predictive-routing-text">

                        <strong>
                            Tourist Navigation
                        </strong>

                        <p>
                            The intended result is a
                            recommended route that balances
                            travel efficiency with geographic
                            safety considerations.
                        </p>

                    </div>

                </div>

            </div>

            <p class="predictive-description">
                This describes the intended relationship
                between predictions and routing.
                It does not confirm that the archived
                July 2026 predictions are currently used
                by Android navigation.
            </p>

        </div>

    </section>

</div>

<!-- Search and Risk Filter -->

<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById(
        'predictiveBarangaySearch'
    );

    const riskFilter = document.getElementById(
        'predictiveRiskFilter'
    );

    const visibleCount = document.getElementById(
        'predictiveVisibleCount'
    );

    const emptyResult = document.getElementById(
        'predictiveEmptyResult'
    );

    const scrollContainer = document.getElementById(
        'predictiveTableScroll'
    );

    const rows = document.querySelectorAll(
        '.predictive-barangay-row'
    );

    if (!searchInput || !riskFilter) {
        return;
    }

    function filterPredictions() {

        const searchValue = searchInput.value
            .trim()
            .toLowerCase();

        const selectedRisk = riskFilter.value;

        let visible = 0;

        rows.forEach(function (row) {

            const barangay = (
                row.dataset.barangay || ''
            ).toLowerCase();

            const risk = row.dataset.risk || '';

            const matchesSearch =
                barangay.includes(searchValue);

            const matchesRisk =
                selectedRisk === 'all' ||
                risk === selectedRisk;

            const shouldShow =
                matchesSearch && matchesRisk;

            row.hidden = !shouldShow;

            row.style.display =
                shouldShow ? '' : 'none';

            if (shouldShow) {
                visible++;
            }

        });

        if (visibleCount) {
            visibleCount.textContent = visible;
        }

        if (emptyResult) {
            emptyResult.hidden = visible !== 0;
        }

        // Return to the first visible result.
        if (scrollContainer) {
            scrollContainer.scrollTop = 0;
        }
    }

    searchInput.addEventListener(
        'input',
        filterPredictions
    );

    riskFilter.addEventListener(
        'change',
        filterPredictions
    );

});
</script>

@endsection
