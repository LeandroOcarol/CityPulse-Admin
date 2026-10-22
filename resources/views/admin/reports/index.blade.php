@extends('admin.layouts.app')

@section('title', 'CityPulse | Reports')
@section('body-class', 'reports-page')

@push('styles')
    <link rel="stylesheet" href="/css/pages/reports.css">
@endpush

@section('content')

    <div class="reports-header">

        <div>

            <h1>
                Operational Intelligence & Reports
            </h1>

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


    <form
        id="report-filter-form"
        method="GET"
        action="{{ route('admin.reports.index') }}"
        class="reports-layout"
    >

        <!-- LEFT SIDE -->

        <div class="reports-main">

            <!-- Report Category -->

            <section class="reports-card">

                <div class="reports-card-header">

                    <div>

                        <span class="reports-step">
                            1
                        </span>

                        <h2>
                            Select Report Category
                        </h2>

                    </div>

                </div>


                <div class="reports-card-body">

                    <div class="report-category-grid">

                        @foreach($reportCategories as $key => $reportCategory)

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

                                    <h3>
                                        {{ $reportCategory['title'] }}
                                    </h3>

                                    <p>
                                        {{ $reportCategory['description'] }}
                                    </p>

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

                        <h2>
                            Report Preview
                        </h2>

                    </div>

                </div>


                <div class="reports-card-body">

                    <div class="report-summary-grid">

                        @foreach($summary as $item)

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

                </div>

            </section>


            <!-- Recent Reports -->

            <section class="reports-card">

                <div class="reports-card-header">

                    <div>

                        <h2>
                            Recent Reports
                        </h2>

                    </div>

                </div>


                <div class="reports-card-body">

                    <div class="reports-empty-state">

                        <strong>
                            No generated reports yet.
                        </strong>

                        <p>
                            Generated report history will appear here once report exporting is enabled.
                        </p>

                    </div>

                </div>

            </section>

        </div>


        <!-- RIGHT SIDE -->

        <aside class="reports-sidebar">

            <!-- Filters -->

            <section class="reports-card">

                <div class="reports-card-header">

                    <div>

                        <span class="reports-step">
                            2
                        </span>

                        <h2>
                            Configure Filters
                        </h2>

                    </div>

                </div>


                <div class="reports-card-body">

                    <div class="report-filter-group">

                        <label>
                            Time Period
                        </label>


                        <div class="report-period-grid">

                            <label
                                class="report-period-button
                                {{ $period === '7_days' ? 'active' : '' }}"
                            >

                                <input
                                    type="radio"
                                    name="period"
                                    value="7_days"
                                    {{ $period === '7_days' ? 'checked' : '' }}
                                >

                                Past 7 Days

                            </label>


                            <label
                                class="report-period-button
                                {{ $period === '30_days' ? 'active' : '' }}"
                            >

                                <input
                                    type="radio"
                                    name="period"
                                    value="30_days"
                                    {{ $period === '30_days' ? 'checked' : '' }}
                                >

                                Past 30 Days

                            </label>


                            <label
                                class="report-period-button
                                {{ $period === 'quarterly' ? 'active' : '' }}"
                            >

                                <input
                                    type="radio"
                                    name="period"
                                    value="quarterly"
                                    {{ $period === 'quarterly' ? 'checked' : '' }}
                                >

                                Quarterly

                            </label>


                            <label
                                class="report-period-button
                                {{ $period === 'annual' ? 'active' : '' }}"
                            >

                                <input
                                    type="radio"
                                    name="period"
                                    value="annual"
                                    {{ $period === 'annual' ? 'checked' : '' }}
                                >

                                Annual

                            </label>

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

                            <strong>
                                Data Visualization
                            </strong>

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

                        <span class="reports-step">
                            3
                        </span>

                        <h2>
                            Export Actions
                        </h2>

                    </div>

                </div>


                <div class="reports-card-body">

                    <button
                        type="button"
                        class="report-export-button report-export-pdf"
                        disabled
                    >

                        <span>
                            Generate PDF
                        </span>

                        <small>
                            High-quality document
                        </small>

                    </button>


                    <button
                        type="button"
                        class="report-export-button report-export-excel"
                        disabled
                    >

                        <span>
                            Export Excel
                        </span>

                        <small>
                            Raw data for analysis
                        </small>

                    </button>


                    <button
                        type="button"
                        class="report-export-button"
                        onclick="window.print()"
                    >

                        <span>
                            Print Report
                        </span>

                        <small>
                            Send to system printer
                        </small>

                    </button>

                </div>

            </section>

        </aside>

    </form>

@endsection


@push('scripts')

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const categoryCards =
                    document.querySelectorAll(
                        '.report-category-card'
                    );

                const periodButtons =
                    document.querySelectorAll(
                        '.report-period-button'
                    );


                categoryCards.forEach(
                    function (card) {

                        const input =
                            card.querySelector('input');


                        input.addEventListener(
                            'change',
                            function () {

                                categoryCards.forEach(
                                    function (item) {

                                        item.classList.remove(
                                            'active'
                                        );

                                    }
                                );


                                card.classList.add(
                                    'active'
                                );

                            }
                        );

                    }
                );


                periodButtons.forEach(
                    function (button) {

                        const input =
                            button.querySelector('input');


                        input.addEventListener(
                            'change',
                            function () {

                                periodButtons.forEach(
                                    function (item) {

                                        item.classList.remove(
                                            'active'
                                        );

                                    }
                                );


                                button.classList.add(
                                    'active'
                                );

                            }
                        );

                    }
                );

            }
        );

    </script>

@endpush