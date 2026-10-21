@extends('admin.layouts.app')

@section('title', 'CityPulse | Crime Records')
@section('body-class', 'crime-records-page')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/pages/crime-records.css') }}"
    >
@endpush

@section('content')

    <div class="crime-records-header">

        <h1>
            Crime Records
        </h1>

        <a
            href="{{ route('admin.crime-records.create') }}"
            class="crime-add-button"
        >
            Add Crime Record
        </a>

    </div>


    @if(session('success'))

        <div class="crime-success">
            {{ session('success') }}
        </div>

    @endif


    <section class="crime-summary-grid">

        <div class="crime-summary-card">

            <div class="crime-summary-content">

                <span class="crime-summary-label">
                    Crime Records
                </span>

                <strong class="crime-summary-value">
                    {{ number_format($totalRecords) }}
                </strong>

            </div>

        </div>


        <div class="crime-summary-card">

            <div class="crime-summary-content">

                <span class="crime-summary-label">
                    Total Crime Count
                </span>

                <strong class="crime-summary-value">
                    {{ number_format($totalCrimeCount) }}
                </strong>

            </div>

        </div>

    </section>


    <section class="crime-filter-card">

        <form
            method="GET"
            action="{{ route('admin.crime-records.index') }}"
            class="crime-filter-form"
        >

            <div class="crime-search">

                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search crime records"
                >

            </div>


            <div class="crime-filter-controls">

                <select name="type">

                    <option value="all">
                        All Types
                    </option>

                    @foreach($crimeTypes as $crimeType)

                        <option
                            value="{{ $crimeType->type_id }}"
                            {{ (string) $type === (string) $crimeType->type_id ? 'selected' : '' }}
                        >
                            {{ $crimeType->incident_name }}
                        </option>

                    @endforeach

                </select>


                <select name="severity">

                    <option value="all">
                        All Severities
                    </option>

                    @foreach($severities as $severityOption)

                        <option
                            value="{{ $severityOption }}"
                            {{ $severity === $severityOption ? 'selected' : '' }}
                        >
                            {{ $severityOption }}
                        </option>

                    @endforeach

                </select>


                <select name="zone">

                    <option value="all">
                        All Zones
                    </option>

                    <option
                        value="unassigned"
                        {{ $zone === 'unassigned' ? 'selected' : '' }}
                    >
                        Unassigned
                    </option>

                    @foreach($zones as $zoneOption)

                        <option
                            value="{{ $zoneOption->zone_id }}"
                            {{ (string) $zone === (string) $zoneOption->zone_id ? 'selected' : '' }}
                        >
                            {{ $zoneOption->zone_name }}
                        </option>

                    @endforeach

                </select>


                <button
                    type="submit"
                    class="crime-filter-button"
                >
                    Filter
                </button>


                @if(
                    $search !== '' ||
                    $type !== 'all' ||
                    $severity !== 'all' ||
                    $zone !== 'all'
                )

                    <a
                        href="{{ route('admin.crime-records.index') }}"
                        class="crime-clear-button"
                    >
                        Clear
                    </a>

                @endif

            </div>

        </form>

    </section>


    <section class="crime-table-card">

        <div class="crime-table-header">

            <div>

                <h2>
                    Crime Directory
                </h2>

                <span>
                    {{ number_format($crimeRecords->total()) }} records
                </span>

            </div>

        </div>


        <div class="crime-table-wrapper">

            <table class="crime-table">

                <thead>

                    <tr>
                        <th>Crime Type</th>
                        <th>Severity</th>
                        <th>Zone</th>
                        <th>Count</th>
                        <th>Crime Date</th>
                        <th>Source</th>
                        <th>Actions</th>
                    </tr>

                </thead>


                <tbody>

                    @forelse($crimeRecords as $record)

                        @php

                            $crimeTypeName =
                                $record->incident_name
                                ?? 'Unknown';

                            $severityClass = strtolower(
                                $record->severity ?? 'low'
                            );

                        @endphp


                        <tr>

                            <td>

                                <div class="crime-type-cell">

                                    <strong>
                                        {{ $crimeTypeName }}
                                    </strong>

                                    @if(!empty($record->report_category))

                                        <span>
                                            {{ $record->report_category }}
                                        </span>

                                    @endif

                                </div>

                            </td>


                            <td>

                                @if(!empty($record->severity))

                                    <span class="crime-severity crime-severity-{{ $severityClass }}">
                                        {{ $record->severity }}
                                    </span>

                                @else

                                    —

                                @endif

                            </td>


                            <td>

                                <div class="crime-zone-cell">

                                    <strong>
                                        {{ $record->zone_name ?? 'Unassigned' }}
                                    </strong>

                                    @if(!empty($record->zone_type))

                                        <span>
                                            {{ $record->zone_type }}
                                        </span>

                                    @endif

                                </div>

                            </td>


                            <td>

                                <strong class="crime-count-value">
                                    {{ number_format($record->crime_count ?? 0) }}
                                </strong>

                            </td>


                            <td>

                                @if(!empty($record->crime_date))

                                    {{
                                        \Carbon\Carbon::parse(
                                            $record->crime_date
                                        )->format('M d, Y')
                                    }}

                                @else

                                    —

                                @endif

                            </td>


                            <td>

                                <span class="crime-source">
                                    {{ $record->source ?? '—' }}
                                </span>

                            </td>


                            <td>

                                <div class="crime-actions">

                                    <a
                                        href="{{ route('admin.crime-records.show', $record->crime_id) }}"
                                        class="crime-view-button"
                                    >
                                        View
                                    </a>

                                    <a
                                        href="{{ route('admin.crime-records.edit', $record->crime_id) }}"
                                        class="crime-edit-button"
                                    >
                                        Edit
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="crime-empty"
                            >
                                No crime records found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <div class="crime-pagination">

            <div class="crime-pagination-info">

                Showing

                <strong>
                    {{ $crimeRecords->firstItem() ?? 0 }}
                </strong>

                –

                <strong>
                    {{ $crimeRecords->lastItem() ?? 0 }}
                </strong>

                of

                <strong>
                    {{ number_format($crimeRecords->total()) }}
                </strong>

            </div>


            <div class="crime-pagination-controls">

                @if($crimeRecords->onFirstPage())

                    <span class="crime-page-button disabled">
                        Previous
                    </span>

                @else

                    <a
                        href="{{ $crimeRecords->previousPageUrl() }}"
                        class="crime-page-button"
                    >
                        Previous
                    </a>

                @endif


                <span class="crime-page-current">

                    {{ $crimeRecords->currentPage() }}
                    /
                    {{ $crimeRecords->lastPage() }}

                </span>


                @if($crimeRecords->hasMorePages())

                    <a
                        href="{{ $crimeRecords->nextPageUrl() }}"
                        class="crime-page-button"
                    >
                        Next
                    </a>

                @else

                    <span class="crime-page-button disabled">
                        Next
                    </span>

                @endif

            </div>

        </div>

    </section>

@endsection