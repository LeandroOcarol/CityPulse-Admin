
@extends('admin.layouts.app')

@section('title', 'CityPulse | Reports')
@section('body-class', 'reports-page')

@push('styles')
    <link rel="stylesheet" href="/css/pages/reports.css">
@endpush

@section('content')

    <div class="reports-header">
        <div>
            <h1>Operational Intelligence & Reports</h1>
            <p>
                Configure and generate municipal tourism and safety reports.
            </p>
        </div>

        <button
            type="submit"
            form="report-filter-form"
            class="reports-generate-button"
        >
            Generate Smart Report
        </button>
    </div>

    @if ($errors->any())
        <div class="reports-card" style="margin-bottom: 20px;">
            <div class="reports-card-body">
                @foreach ($errors->all() as $error)
                    <p style="color: #b91c1c;">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <form
        id="report-filter-form"
        method="GET"
        action="{{ route('admin.reports.index') }}"
        class="reports-layout"
    >

        <div class="reports-main">

            <!-- Report Category -->
            <section class="reports-card">
                <div class="reports-card-header">
                    <div>
                        <span class="reports-step">1</span>
                        <h2>Select Report Category</h2>
                    </div>
                </div>

                <div class="reports-card-body">
                    <div class="report-category-grid">

                        @foreach ($reportCategories as $key => $reportCategory)
                            <label
                                class="report-category-card
                                {{ $selectedCategory === $key ? 'active' : '' }}"
                            >
                                <input
                                    type="radio"
                                    name="category"
                                    value="{{ $key }}"
                                    {{ $selectedCategory === $key ? 'checked' : '' }}
                                >

                                <div class="report-category-content">
                                    <h3>{{ $reportCategory['title'] }}</h3>
                                    <p>{{ $reportCategory['description'] }}</p>
                                </div>
                            </label>
                        @endforeach

                    </div>
                </div>
            </section>

            <!-- Report Preview -->
            <section class="reports-card">
                <div class="reports-card-header">
                    <div>
                        <h2>Report Preview</h2>
                    </div>
                </div>

                <div class="reports-card-body">

                    <p style="margin-bottom: 16px;">
                        <strong>{{ $selectedTitle }}</strong>
                        <span style="display: block; margin-top: 5px;">
                            {{ $periodLabel }}:
                            {{ $fromDisplay }} – {{ $toDisplay }}
                        </span>
                    </p>

                    <div class="report-summary-grid">
                        @foreach ($summary as $item)
                            <div class="report-summary-card">
                                <span class="report-summary-label">
                                    {{ $item['label'] }}
                                </span>

                                <strong class="report-summary-value">
                                    {{ number_format($item['value']) }}
                                </strong>
                            </div>
                        @endforeach
                    </div>

                    @if ($visualization)
                        @php
                            $maxSummary = max(
                                1,
                                ...array_column($summary, 'value')
                            );
                        @endphp

                        <div style="margin-top: 24px;">
                            <h3 style="margin-bottom: 14px;">
                                Data Visualization
                            </h3>

                            @foreach ($summary as $item)
                                @php
                                    $percentage = $maxSummary > 0
                                        ? ($item['value'] / $maxSummary) * 100
                                        : 0;
                                @endphp

                                <div style="margin-bottom: 14px;">
                                    <div style="display: flex; justify-content: space-between; gap: 12px; margin-bottom: 6px;">
                                        <span>{{ $item['label'] }}</span>
                                        <strong>
                                            {{ number_format($item['value']) }}
                                        </strong>
                                    </div>

                                    <div style="height: 10px; background: #e5e7eb; border-radius: 5px; overflow: hidden;">
                                        <div
                                            style="height: 100%; background: #25824d; width: {{ $percentage }}%;"
                                        ></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div style="margin-top: 24px;">
                        <h3 style="margin-bottom: 12px;">
                            Report Details
                        </h3>

                        <p style="margin-bottom: 12px;">
                            {{ number_format($recordCount) }}
                            record(s) found.
                            Showing up to 20 records in the preview.
                        </p>

                        <div style="overflow-x: auto;">
                            <table
                                style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;"
                            >
                                <thead>
                                    <tr>
                                        @foreach ($headers as $header)
                                            <th
                                                style="padding: 10px; border-bottom: 2px solid #e5e7eb; white-space: nowrap;"
                                            >
                                                {{ $header }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($previewRows as $row)
                                        <tr>
                                            @foreach ($row as $value)
                                                <td
                                                    style="padding: 10px; border-bottom: 1px solid #e5e7eb; vertical-align: top;"
                                                >
                                                    {{ $value }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td
                                                colspan="{{ count($headers) }}"
                                                style="padding: 20px; text-align: center;"
                                            >
                                                No records found for the selected period.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($recordCount > 20)
                            <p style="margin-top: 12px;">
                                PDF and Excel exports include all matching records.
                            </p>
                        @endif
                    </div>

                </div>
            </section>

            <!-- Recent Reports -->
            <section class="reports-card">
                <div class="reports-card-header">
                    <div>
                        <h2>Recent Reports</h2>
                    </div>
                </div>

                <div class="reports-card-body">
                    <div class="reports-empty-state">
                        <strong>
                            No saved report history yet.
                        </strong>

                        <p>
                            Reports can now be downloaded as PDF or Excel.
                            Saved export history will be added separately.
                        </p>
                    </div>
                </div>
            </section>

        </div>

        <aside class="reports-sidebar">

            <!-- Filters -->
            <section class="reports-card">
                <div class="reports-card-header">
                    <div>
                        <span class="reports-step">2</span>
                        <h2>Configure Filters</h2>
                    </div>
                </div>

                <div class="reports-card-body">

                    <div class="report-filter-group">
                        <label>Time Period</label>

                        <div class="report-period-grid">

                            @foreach ([
                                '7_days' => 'Past 7 Days',
                                '30_days' => 'Past 30 Days',
                                'quarterly' => 'Quarterly',
                                'annual' => 'Annual',
                            ] as $key => $label)

                                <label
                                    class="report-period-button
                                    {{ $period === $key ? 'active' : '' }}"
                                >
                                    <input
                                        type="radio"
                                        name="period"
                                        value="{{ $key }}"
                                        {{ $period === $key ? 'checked' : '' }}
                                    >
                                    {{ $label }}
                                </label>

                            @endforeach

                        </div>
                    </div>

                    <div class="report-filter-group">
                        <label for="start_date">
                            Start Date
                        </label>

                        <input
                            id="start_date"
                            type="date"
                            name="start_date"
                            value="{{ $startDate }}"
                        >
                    </div>

                    <div class="report-filter-group">
                        <label for="end_date">
                            End Date
                        </label>

                        <input
                            id="end_date"
                            type="date"
                            name="end_date"
                            value="{{ $endDate }}"
                        >
                    </div>

                    <div class="report-visualization-row">
                        <div>
                            <strong>Data Visualization</strong>
                            <span>
                                Include charts and visual summaries.
                            </span>
                        </div>

                        <label class="report-switch">
                            <input
                                type="checkbox"
                                name="visualization"
                                value="1"
                                {{ $visualization ? 'checked' : '' }}
                            >
                            <span></span>
                        </label>
                    </div>

                </div>
            </section>

            <!-- Export Actions -->
            <section class="reports-card">
                <div class="reports-card-header">
                    <div>
                        <span class="reports-step">3</span>
                        <h2>Export Actions</h2>
                    </div>
                </div>

                <div class="reports-card-body">

                    <button
                        type="submit"
                        formaction="{{ route('admin.reports.export.pdf') }}"
                        formmethod="GET"
                        class="report-export-button report-export-pdf"
                    >
                        <span>Generate PDF</span>
                        <small>High-quality document</small>
                    </button>

                    <button
                        type="submit"
                        formaction="{{ route('admin.reports.export.excel') }}"
                        formmethod="GET"
                        class="report-export-button report-export-excel"
                    >
                        <span>Export Excel</span>
                        <small>Raw data for analysis</small>
                    </button>

                    <button
                        type="button"
                        class="report-export-button"
                        onclick="window.print()"
                    >
                        <span>Print Report</span>
                        <small>Send to system printer</small>
                    </button>

                </div>
            </section>

        </aside>

    </form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const categoryCards = document.querySelectorAll('.report-category-card');
    const periodButtons = document.querySelectorAll('.report-period-button');

    function updateActiveCards(cards) {
        cards.forEach(function (card) {
            const input = card.querySelector('input');
            card.classList.toggle('active', input.checked);
        });
    }

    categoryCards.forEach(function (card) {
        card.querySelector('input').addEventListener('change', function () {
            updateActiveCards(categoryCards);
        });
    });

    periodButtons.forEach(function (button) {
        button.querySelector('input').addEventListener('change', function () {
            updateActiveCards(periodButtons);

            // A newly selected preset should replace a previous custom range.
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';
        });
    });

});
</script>
@endpush
