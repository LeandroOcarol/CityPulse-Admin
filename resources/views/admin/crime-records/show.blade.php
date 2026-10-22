@extends('admin.layouts.app')

@section('title', 'CityPulse | Crime Record')
@section('body-class', 'crime-record-show-page')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/pages/crime-record-show.css') }}"
    >
@endpush

@section('content')

    @php

        $crimeType =
            $record->incident_name
            ?? 'Unknown';

        $severityClass = strtolower(
            $record->severity ?? 'low'
        );

    @endphp


    <div class="crime-show-header">

        <div>

            <a
                href="{{ route('admin.crime-records.index') }}"
                class="crime-show-back"
            >
                Back to Crime Records
            </a>

            <h1>
                Crime Record
            </h1>

        </div>


        <div class="crime-show-header-actions">

            <a
                href="{{ route('admin.crime-records.edit', $record->crime_id) }}"
                class="crime-show-edit"
            >
                Edit Record
            </a>


            <form
                method="POST"
                action="{{ route('admin.crime-records.destroy', $record->crime_id) }}"
                class="crime-show-delete-form"
                onsubmit="return confirm('Are you sure you want to permanently delete this crime record?');"
            >
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="crime-show-delete"
                >
                    Delete Record
                </button>

            </form>

        </div>

    </div>


    @if(session('success'))

        <div class="crime-show-success">
            {{ session('success') }}
        </div>

    @endif


    <section class="crime-show-overview">

        <div class="crime-show-overview-main">

            <div>

                <h2>
                    {{ $crimeType }}
                </h2>

                <div class="crime-show-meta">

                    @if(!empty($record->severity))

                        <span class="crime-show-severity crime-show-severity-{{ $severityClass }}">
                            {{ $record->severity }}
                        </span>

                    @endif


                    @if(!empty($record->report_category))

                        <span class="crime-show-category">
                            {{ $record->report_category }}
                        </span>

                    @endif

                </div>

            </div>


            <div class="crime-show-id">

                <span>
                    Record ID
                </span>

                <strong>
                    #{{ $record->crime_id }}
                </strong>

            </div>

        </div>

    </section>


    <div class="crime-show-grid">

        <div class="crime-show-column">

            <section class="crime-show-card">

                <div class="crime-show-card-header">
                    <h2>Crime Information</h2>
                </div>


                <div class="crime-show-card-body">

                    <div class="crime-show-row">
                        <span class="crime-show-label">Crime Type</span>
                        <span class="crime-show-value">{{ $crimeType }}</span>
                    </div>

                    <div class="crime-show-row">
                        <span class="crime-show-label">Category</span>
                        <span class="crime-show-value">{{ $record->report_category ?? '—' }}</span>
                    </div>

                    <div class="crime-show-row">
                        <span class="crime-show-label">Severity</span>
                        <span class="crime-show-value">{{ $record->severity ?? '—' }}</span>
                    </div>

                    <div class="crime-show-row">
                        <span class="crime-show-label">Crime Count</span>
                        <span class="crime-show-value">{{ number_format($record->crime_count ?? 0) }}</span>
                    </div>

                    <div class="crime-show-row">

                        <span class="crime-show-label">
                            Crime Date
                        </span>

                        <span class="crime-show-value">

                            @if(!empty($record->crime_date))

                                {{
                                    \Carbon\Carbon::parse(
                                        $record->crime_date
                                    )->format('F d, Y')
                                }}

                            @else

                                —

                            @endif

                        </span>

                    </div>

                </div>

            </section>


            <section class="crime-show-card">

                <div class="crime-show-card-header">
                    <h2>Location</h2>
                </div>


                <div class="crime-show-card-body">

                    <div class="crime-show-row">

                        <span class="crime-show-label">
                            Latitude
                        </span>

                        <span class="crime-show-value">

                            @if($record->latitude !== null)

                                {{ number_format($record->latitude, 6) }}

                            @else

                                —

                            @endif

                        </span>

                    </div>


                    <div class="crime-show-row">

                        <span class="crime-show-label">
                            Longitude
                        </span>

                        <span class="crime-show-value">

                            @if($record->longitude !== null)

                                {{ number_format($record->longitude, 6) }}

                            @else

                                —

                            @endif

                        </span>

                    </div>

                </div>

            </section>

        </div>


        <div class="crime-show-column">

            <section class="crime-show-card">

                <div class="crime-show-card-header">
                    <h2>Geofence Zone</h2>
                </div>


                <div class="crime-show-card-body">

                    <div class="crime-show-row">
                        <span class="crime-show-label">Zone</span>
                        <span class="crime-show-value">{{ $record->zone_name ?? 'Unassigned' }}</span>
                    </div>

                    <div class="crime-show-row">
                        <span class="crime-show-label">Zone Type</span>
                        <span class="crime-show-value">{{ $record->zone_type ?? '—' }}</span>
                    </div>

                    <div class="crime-show-row">

                        <span class="crime-show-label">
                            Radius
                        </span>

                        <span class="crime-show-value">

                            @if($record->radius_meters !== null)

                                {{ number_format($record->radius_meters) }} meters

                            @else

                                —

                            @endif

                        </span>

                    </div>

                </div>

            </section>


            <section class="crime-show-card crime-source-card">

                <div class="crime-show-card-header">
                    <h2>Source</h2>
                </div>

                <div class="crime-show-text">
                    <p>{{ $record->source ?? '—' }}</p>
                </div>

            </section>


            @if(!empty($record->alert_message))

                <section class="crime-show-card crime-alert-card">

                    <div class="crime-show-card-header">
                        <h2>Zone Alert Message</h2>
                    </div>

                    <div class="crime-show-text">
                        <p>{{ $record->alert_message }}</p>
                    </div>

                </section>

            @endif

        </div>

    </div>


    <section class="crime-show-card crime-metadata-card">

        <div class="crime-show-card-header">
            <h2>Record Information</h2>
        </div>


        <div class="crime-show-card-body">

            <div class="crime-show-row">

                <span class="crime-show-label">
                    Created
                </span>

                <span class="crime-show-value">

                    @if(!empty($record->created_at))

                        {{
                            \Carbon\Carbon::parse(
                                $record->created_at
                            )->format('F d, Y h:i A')
                        }}

                    @else

                        —

                    @endif

                </span>

            </div>

        </div>

    </section>

@endsection