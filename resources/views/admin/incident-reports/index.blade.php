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

    <div class="incident-header">
        <h1>Incident Reports</h1>
    </div>


    @if(session('success'))

        <div class="incident-success">
            {{ session('success') }}
        </div>

    @endif


    <section class="incident-summary-grid">

        <div class="incident-summary-card">

            <span class="incident-summary-label">
                Incident Reports
            </span>

            <strong class="incident-summary-value">
                {{ number_format($totalIncidents) }}
            </strong>

        </div>


        <div class="incident-summary-card">

            <span class="incident-summary-label">
                Pending
            </span>

            <strong class="incident-summary-value">
                {{ number_format($pendingIncidents) }}
            </strong>

        </div>

    </section>


    <section class="incident-filter-card">

        <form
            method="GET"
            action="{{ route('admin.incident-reports.index') }}"
            class="incident-filter-form"
        >

            <div class="incident-search">

                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search incident reports"
                >

            </div>


            <div class="incident-filter-controls">

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

                    <option
                        value="all"
                        {{ $status === 'all' ? 'selected' : '' }}
                    >
                        All Statuses
                    </option>

                    <option
                        value="Pending"
                        {{ $status === 'Pending' ? 'selected' : '' }}
                    >
                        Pending
                    </option>

                    <option
                        value="Approved"
                        {{ $status === 'Approved' ? 'selected' : '' }}
                    >
                        Approved
                    </option>

                    <option
                        value="Rejected"
                        {{ $status === 'Rejected' ? 'selected' : '' }}
                    >
                        Rejected
                    </option>

                </select>


                <button
                    type="submit"
                    class="incident-filter-button"
                >
                    Filter
                </button>


                @if(
                    $search !== '' ||
                    $type !== 'all' ||
                    $status !== 'all'
                )

                    <a
                        href="{{ route('admin.incident-reports.index') }}"
                        class="incident-clear-button"
                    >
                        Clear
                    </a>

                @endif

            </div>

        </form>

    </section>


    <section class="incident-table-card">

        <div class="incident-table-header">

            <div>

                <h2>
                    Incident Directory
                </h2>

                <span>
                    {{ number_format($incidents->total()) }} records
                </span>

            </div>

        </div>


        <div class="incident-table-wrapper">

            <table class="incident-table">

                <thead>

                    <tr>
                        <th>Report</th>
                        <th>Type</th>
                        <th>Reporter</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>

                </thead>


                <tbody>

                    @forelse($incidents as $incident)

                        @php

                            $reporterName = trim(
                                ($incident->first_name ?? '') . ' ' .
                                ($incident->last_name ?? '')
                            );

                            if ($reporterName === '') {
                                $reporterName =
                                    $incident->email
                                    ?? 'Unknown';
                            }

                            $incidentType =
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
                                        {{ $incident->title }}
                                    </strong>

                                    <span>
                                        #{{ $incident->incident_id }}
                                    </span>

                                </div>

                            </td>


                            <td>
                                {{ $incidentType }}
                            </td>


                            <td>

                                <div class="incident-reporter-cell">

                                    <strong>
                                        {{ $reporterName }}
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
                                    {{ $incident->status }}
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

                                <div class="incident-actions">

                                    <a
                                        href="{{ route('admin.incident-reports.show', $incident->incident_id) }}"
                                        class="incident-view-button"
                                    >
                                        View
                                    </a>


                                    <form
                                        method="POST"
                                        action="{{ route('admin.incident-reports.destroy', $incident->incident_id) }}"
                                        class="incident-delete-form"
                                        onsubmit="return confirm('Are you sure you want to permanently delete this incident report?');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="incident-delete-button"
                                        >
                                            Delete
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="incident-empty"
                            >
                                No incident reports found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <div class="incident-pagination">

            <div class="incident-pagination-info">

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
                    {{ number_format($incidents->total()) }}
                </strong>

            </div>


            <div class="incident-pagination-controls">

                @if($incidents->onFirstPage())

                    <span class="incident-page-button disabled">
                        Previous
                    </span>

                @else

                    <a
                        href="{{ $incidents->previousPageUrl() }}"
                        class="incident-page-button"
                    >
                        Previous
                    </a>

                @endif


                <span class="incident-page-current">
                    {{ $incidents->currentPage() }}
                    /
                    {{ $incidents->lastPage() }}
                </span>


                @if($incidents->hasMorePages())

                    <a
                        href="{{ $incidents->nextPageUrl() }}"
                        class="incident-page-button"
                    >
                        Next
                    </a>

                @else

                    <span class="incident-page-button disabled">
                        Next
                    </span>

                @endif

            </div>

        </div>

    </section>

@endsection