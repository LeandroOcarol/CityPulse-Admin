@extends('admin.layouts.app')

@section('title', 'CityPulse | Incident Report')
@section('body-class', 'incident-report-show-page')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/pages/incident-report-show.css') }}"
    >
@endpush

@section('content')

    @php

        $incidentType =
            $incident->incident_name
            ?? $incident->other_type
            ?? 'Other';

        $reporterName = trim(
            ($incident->first_name ?? '') . ' ' .
            ($incident->last_name ?? '')
        );

        if ($reporterName === '') {
            $reporterName =
                $incident->email
                ?? 'Unknown';
        }

        $statusClass = strtolower(
            $incident->status ?? 'pending'
        );

    @endphp


    <div class="incident-show-header">

        <div>

            <a
                href="{{ route('admin.incident-reports.index') }}"
                class="incident-show-back"
            >
                Back to Incident Reports
            </a>

            <h1>
                Incident Report
            </h1>

        </div>


        <form
            method="POST"
            action="{{ route('admin.incident-reports.destroy', $incident->incident_id) }}"
            class="incident-show-delete-form"
            onsubmit="return confirm('Are you sure you want to permanently delete this incident report?');"
        >
            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="incident-show-delete"
            >
                Delete Report
            </button>

        </form>

    </div>


    @if(session('success'))

        <div class="incident-show-success">
            {{ session('success') }}
        </div>

    @endif


    <section class="incident-show-overview">

        <div class="incident-show-overview-main">

            <div>

                <h2>
                    {{ $incident->title }}
                </h2>

                <div class="incident-show-meta">

                    <span class="incident-show-status incident-show-status-{{ $statusClass }}">
                        {{ $incident->status }}
                    </span>

                    <span class="incident-show-category">
                        {{ $incidentType }}
                    </span>

                </div>

            </div>


            <div class="incident-show-id">

                <span>
                    Incident ID
                </span>

                <strong>
                    #{{ $incident->incident_id }}
                </strong>

            </div>

        </div>

    </section>


    @if($incident->status === 'Pending')

        <section class="incident-review-card">

            <div class="incident-review-content">

                <div>

                    <h2>
                        Review Report
                    </h2>

                    <p>
                        Approve or reject this submitted incident report.
                    </p>

                </div>


                <div class="incident-review-actions">

                    <form
                        method="POST"
                        action="{{ route('admin.incident-reports.status', $incident->incident_id) }}"
                    >
                        @csrf
                        @method('PATCH')

                        <input
                            type="hidden"
                            name="status"
                            value="Approved"
                        >

                        <button
                            type="submit"
                            class="incident-approve-button"
                            onclick="return confirm('Approve this incident report?');"
                        >
                            Approve
                        </button>

                    </form>


                    <form
                        method="POST"
                        action="{{ route('admin.incident-reports.status', $incident->incident_id) }}"
                    >
                        @csrf
                        @method('PATCH')

                        <input
                            type="hidden"
                            name="status"
                            value="Rejected"
                        >

                        <button
                            type="submit"
                            class="incident-reject-button"
                            onclick="return confirm('Reject this incident report?');"
                        >
                            Reject
                        </button>

                    </form>

                </div>

            </div>

        </section>

    @endif


    <div class="incident-show-grid">

        <div class="incident-show-column">

            <section class="incident-show-card">

                <div class="incident-show-card-header">
                    <h2>Report Information</h2>
                </div>


                <div class="incident-show-card-body">

                    <div class="incident-show-row">
                        <span class="incident-show-label">Incident Type</span>
                        <span class="incident-show-value">{{ $incidentType }}</span>
                    </div>

                    <div class="incident-show-row">
                        <span class="incident-show-label">Category</span>
                        <span class="incident-show-value">{{ $incident->report_category ?? '—' }}</span>
                    </div>

                    <div class="incident-show-row">
                        <span class="incident-show-label">Status</span>
                        <span class="incident-show-value">{{ $incident->status }}</span>
                    </div>

                    <div class="incident-show-row">

                        <span class="incident-show-label">
                            Submitted
                        </span>

                        <span class="incident-show-value">

                            @if(!empty($incident->created_at))

                                {{
                                    \Carbon\Carbon::parse(
                                        $incident->created_at
                                    )->format('F d, Y h:i A')
                                }}

                            @else

                                —

                            @endif

                        </span>

                    </div>


                    <div class="incident-show-row">

                        <span class="incident-show-label">
                            Reviewed
                        </span>

                        <span class="incident-show-value">

                            @if(!empty($incident->reviewed_at))

                                {{
                                    \Carbon\Carbon::parse(
                                        $incident->reviewed_at
                                    )->format('F d, Y h:i A')
                                }}

                            @else

                                Not reviewed
                            @endif

                        </span>

                    </div>

                </div>

            </section>


            <section class="incident-show-card">

                <div class="incident-show-card-header">
                    <h2>Reporter</h2>
                </div>


                <div class="incident-show-card-body">

                    <div class="incident-show-row">
                        <span class="incident-show-label">Name</span>
                        <span class="incident-show-value">{{ $reporterName }}</span>
                    </div>

                    <div class="incident-show-row">
                        <span class="incident-show-label">Email</span>
                        <span class="incident-show-value">{{ $incident->email ?? '—' }}</span>
                    </div>

                    <div class="incident-show-row">
                        <span class="incident-show-label">Phone</span>
                        <span class="incident-show-value">{{ $incident->phone_number ?? '—' }}</span>
                    </div>

                </div>

            </section>


            <section class="incident-show-card">

                <div class="incident-show-card-header">
                    <h2>Location</h2>
                </div>


                <div class="incident-show-card-body">

                    <div class="incident-show-row">
                        <span class="incident-show-label">Zone</span>
                        <span class="incident-show-value">{{ $incident->zone_name ?? 'Unassigned' }}</span>
                    </div>

                    <div class="incident-show-row">
                        <span class="incident-show-label">Zone Type</span>
                        <span class="incident-show-value">{{ $incident->zone_type ?? '—' }}</span>
                    </div>

                    <div class="incident-show-row">

                        <span class="incident-show-label">
                            Latitude
                        </span>

                        <span class="incident-show-value">

                            @if($incident->latitude !== null)
                                {{ number_format($incident->latitude, 6) }}
                            @else
                                —
                            @endif

                        </span>

                    </div>

                    <div class="incident-show-row">

                        <span class="incident-show-label">
                            Longitude
                        </span>

                        <span class="incident-show-value">

                            @if($incident->longitude !== null)
                                {{ number_format($incident->longitude, 6) }}
                            @else
                                —
                            @endif

                        </span>

                    </div>

                </div>

            </section>

        </div>


        <div class="incident-show-column">

            <section class="incident-show-card incident-description-card">

                <div class="incident-show-card-header">
                    <h2>Description</h2>
                </div>

                <div class="incident-show-text">
                    <p>{{ $incident->description ?? '—' }}</p>
                </div>

            </section>


            @if(!empty($incident->image_url))

                <section class="incident-show-card">

                    <div class="incident-show-card-header">
                        <h2>Incident Image</h2>
                    </div>

                    <div class="incident-image-container">

                        <img
                            src="{{ $incident->image_url }}"
                            alt="Incident report image"
                            class="incident-image"
                        >

                    </div>

                </section>

            @endif

        </div>

    </div>

@endsection