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

        $reporterName = trim(
            ($incident->first_name ?? '') .
            ' ' .
            ($incident->last_name ?? '')
        );

        $incidentType =
            $incident->incident_name
            ?? $incident->other_type
            ?? 'Other';

        $statusClass = strtolower(
            $incident->status ?? 'pending'
        );

    @endphp


    <!-- Header -->

    <div class="incident-show-header">

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


    <!-- Feedback -->

    @if(session('success'))

        <div class="incident-show-success">
            {{ session('success') }}
        </div>

    @endif


    <!-- Overview -->

    <section class="incident-show-overview">

        <div class="incident-show-overview-main">

            <div>

                <h2>
                    {{ $incident->title ?? 'Incident Report' }}
                </h2>

                <div class="incident-show-meta">

                    <span class="incident-show-type">
                        {{ $incidentType }}
                    </span>

                    <span class="incident-show-status incident-show-status-{{ $statusClass }}">
                        {{ $incident->status ?? 'Pending' }}
                    </span>

                </div>

            </div>


            <div class="incident-show-id">

                <span>
                    Report ID
                </span>

                <strong>
                    #{{ $incident->incident_id }}
                </strong>

            </div>

        </div>

    </section>


    <!-- Review Actions -->

    <section class="incident-review-card">

        <div class="incident-review-info">

            <h2>
                Review Report
            </h2>

            <p>
                Verify the submitted incident and select the appropriate status.
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
                    class="incident-action-button incident-approve-button"
                    {{ $incident->status === 'Approved' ? 'disabled' : '' }}
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
                    class="incident-action-button incident-reject-button"
                    {{ $incident->status === 'Rejected' ? 'disabled' : '' }}
                >
                    Reject
                </button>

            </form>

        </div>

    </section>


    <!-- Main Grid -->

    <div class="incident-show-grid">


        <!-- Left Column -->

        <div class="incident-show-column">

            <section class="incident-show-card">

                <div class="incident-show-card-header">
                    <h2>Report Information</h2>
                </div>


                <div class="incident-show-card-body">

                    <div class="incident-show-row">

                        <span class="incident-show-label">
                            Incident Type
                        </span>

                        <span class="incident-show-value">
                            {{ $incidentType }}
                        </span>

                    </div>


                    <div class="incident-show-row">

                        <span class="incident-show-label">
                            Category
                        </span>

                        <span class="incident-show-value">
                            {{ $incident->report_category ?? '—' }}
                        </span>

                    </div>


                    <div class="incident-show-row">

                        <span class="incident-show-label">
                            Status
                        </span>

                        <span class="incident-show-value">
                            {{ $incident->status ?? 'Pending' }}
                        </span>

                    </div>


                    <div class="incident-show-row">

                        <span class="incident-show-label">
                            Reported
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

                                Not yet reviewed
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

                        <span class="incident-show-label">
                            Name
                        </span>

                        <span class="incident-show-value">
                            {{ $reporterName !== '' ? $reporterName : 'Tourist' }}
                        </span>

                    </div>


                    <div class="incident-show-row">

                        <span class="incident-show-label">
                            Email
                        </span>

                        <span class="incident-show-value">
                            {{ $incident->email ?? '—' }}
                        </span>

                    </div>


                    <div class="incident-show-row">

                        <span class="incident-show-label">
                            Phone
                        </span>

                        <span class="incident-show-value">
                            {{ $incident->phone_number ?? '—' }}
                        </span>

                    </div>

                </div>

            </section>


            <section class="incident-show-card">

                <div class="incident-show-card-header">
                    <h2>Location</h2>
                </div>


                <div class="incident-show-card-body">

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


        <!-- Right Column -->

        <div class="incident-show-column">

            <section class="incident-show-card incident-description-card">

                <div class="incident-show-card-header">
                    <h2>Description</h2>
                </div>

                <div class="incident-show-text">
                    <p>{{ $incident->description ?? '—' }}</p>
                </div>

            </section>


            <section class="incident-show-card incident-image-card">

                <div class="incident-show-card-header">
                    <h2>Attached Image</h2>
                </div>


                <div class="incident-show-image-area">

                    @if(!empty($incident->image_url))

                        <img
                            src="{{ $incident->image_url }}"
                            alt="Incident report attachment"
                        >

                    @else

                        <span>
                            No image attached.
                        </span>

                    @endif

                </div>

            </section>

        </div>

    </div>

@endsection