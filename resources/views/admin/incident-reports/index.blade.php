@extends('admin.layouts.app')

@section('title', 'CityPulse | Incident Reports')
@section('body-class', 'incident-reports-page')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/pages/incident-reports.css') }}"
    >
@endpush

@section('content')

    <div class="incidents-header">

        <h1>
            Incident Reports
        </h1>

    </div>


    <section class="incidents-summary-grid">

        <div class="incidents-summary-card">

            <div class="incidents-summary-content">

                <span class="incidents-summary-label">
                    Incident Reports
                </span>

                <strong class="incidents-summary-value">
                    {{ number_format($totalIncidents) }}
                </strong>

            </div>

        </div>


        <div class="incidents-summary-card">

            <div class="incidents-summary-content">

                <span class="incidents-summary-label">
                    Pending
                </span>

                <strong class="incidents-summary-value">
                    {{ number_format($pendingIncidents) }}
                </strong>

            </div>

        </div>

    </section>


    <section class="incidents-filter-card">

        <form
            method="GET"
            action="{{ route('admin.incident-reports.index') }}"
            class="incidents-filter-form"
        >

            <div class="incidents-search">

                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search reports"
                >

            </div>


            <div class="incidents-filter-controls">

                <select name="type">

                    <option value="all">
                        All Types
                    </option>

                    @foreach($incidentTypes as $incidentType)

                        <option
                            value="{{ $incidentType->type_id }}"
                            {{ (string) $type === (string) $incidentType->type_id ? 'selected' : '' }}
                        >
                            {{ $incidentType->incident_name }}
                        </option>

                    @endforeach

                </select>


                <select name="status">

                    <option value="all">
                        All Statuses
                    </option>

                    @foreach($statuses as $statusOption)

                        <option
                            value="{{ $statusOption }}"
                            {{ $status === $statusOption ? 'selected' : '' }}
                        >
                            {{ $statusOption }}
                        </option>

                    @endforeach

                </select>


                <button
                    type="submit"
                    class="incidents-filter-button"
                >
                    Filter
                </button>


                @if(
                    $search !== '' ||
                    $status !== 'all' ||
                    $type !== 'all'
                )

                    <a
                        href="{{ route('admin.incident-reports.index') }}"
                        class="incidents-clear-button"
                    >
                        Clear
                    </a>

                @endif

            </div>

        </form>

    </section>


    <section class="incidents-table-card">

        <div class="incidents-table-header">

            <div>

                <h2>
                    Report Directory
                </h2>

                <span>
                    {{ $incidents->total() }} reports
                </span>

            </div>

        </div>


        <div class="incidents-table-wrapper">

            <table class="incidents-table">

                <thead>

                    <tr>
                        <th>Report</th>
                        <th>Type</th>
                        <th>Reporter</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>

                </thead>


                <tbody>

                    @forelse($incidents as $incident)

                        @php

                            $reporterName = trim(
                                ($incident->first_name ?? '') .
                                ' ' .
                                ($incident->last_name ?? '')
                            );

                            $incidentTypeName =
                                $incident->incident_name
                                ?? $incident->other_type
                                ?? 'Other';

                            $statusClass = strtolower(
                                $incident->status ?? 'pending'
                            );

                        @endphp


                        <tr>

                            <td>

                                <div class="incident-report-cell">

                                    <strong>
                                        {{ $incident->title ?? 'Incident Report' }}
                                    </strong>

                                    @if(!empty($incident->description))

                                        <span>
                                            {{
                                                \Illuminate\Support\Str::limit(
                                                    $incident->description,
                                                    55
                                                )
                                            }}
                                        </span>

                                    @endif

                                </div>

                            </td>


                            <td>

                                <div class="incident-type-cell">

                                    <strong>
                                        {{ $incidentTypeName }}
                                    </strong>

                                    @if(!empty($incident->report_category))

                                        <span>
                                            {{ $incident->report_category }}
                                        </span>

                                    @endif

                                </div>

                            </td>


                            <td>

                                <div class="incident-reporter-cell">

                                    <strong>
                                        {{ $reporterName !== '' ? $reporterName : 'Tourist' }}
                                    </strong>

                                    @if(!empty($incident->email))

                                        <span>
                                            {{ $incident->email }}
                                        </span>

                                    @endif

                                </div>

                            </td>


                            <td>

                                <span class="incident-status incident-status-{{ $statusClass }}">
                                    {{ $incident->status ?? 'Pending' }}
                                </span>

                            </td>


                            <td>

                                @if(!empty($incident->created_at))

                                    {{
                                        \Carbon\Carbon::parse(
                                            $incident->created_at
                                        )->format('M d, Y')
                                    }}

                                @else

                                    —

                                @endif

                            </td>


                            <td>

                                <a
                                    href="{{ route('admin.incident-reports.show', $incident->incident_id) }}"
                                    class="incident-view-button"
                                >
                                    View
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="incidents-empty"
                            >
                                No incident reports found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <div class="incidents-pagination">

            <div class="incidents-pagination-info">

                Showing

                <strong>
                    {{ $incidents->firstItem() ?? 0 }}
                </strong>

                –

                <strong>
                    {{ $incidents->lastItem() ?? 0 }}
                </strong>

                of

                <strong>
                    {{ $incidents->total() }}
                </strong>

            </div>


            <div class="incidents-pagination-controls">

                @if($incidents->onFirstPage())

                    <span class="incidents-page-button disabled">
                        Previous
                    </span>

                @else

                    <a
                        href="{{ $incidents->previousPageUrl() }}"
                        class="incidents-page-button"
                    >
                        Previous
                    </a>

                @endif


                <span class="incidents-page-current">

                    {{ $incidents->currentPage() }}
                    /
                    {{ $incidents->lastPage() }}

                </span>


                @if($incidents->hasMorePages())

                    <a
                        href="{{ $incidents->nextPageUrl() }}"
                        class="incidents-page-button"
                    >
                        Next
                    </a>

                @else

                    <span class="incidents-page-button disabled">
                        Next
                    </span>

                @endif

            </div>

        </div>

    </section>

@endsection