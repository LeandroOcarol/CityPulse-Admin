@extends('admin.layouts.app')

@section('title', 'CityPulse | Dashboard')
@section('body-class', 'dashboard-page')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/pages/dashboard.css') }}"
    >
@endpush


@section('content')

    <!-- Header -->

    <div class="dashboard-header">

        <h1>
            Dashboard
        </h1>

    </div>


    <!-- Quick Actions -->

    <section class="dashboard-actions">

        <a
            href="{{ route('admin.tourist-spots.create') }}"
            class="action-button action-button-green"
        >
            Add Tourist Spot
        </a>


        <a
            href="{{ route('admin.crime-records.create') }}"
            class="action-button action-button-orange"
        >
            Add Crime Record
        </a>


        <a
            href="{{ route('admin.safety-alerts.create') }}"
            class="action-button action-button-green"
        >
            Create Safety Alert
        </a>


        <a
            href="{{ route('admin.reports.index') }}"
            class="action-button action-button-dark"
        >
            Generate Report
        </a>

    </section>


    <!-- Summary Cards -->

    <section class="dashboard-stats">

        <div class="dashboard-stat-card">

            <div class="dashboard-stat-title">
                Total Tourists
            </div>

            <div class="dashboard-stat-value">
                {{ number_format($totalTourists) }}
            </div>

        </div>


        <div class="dashboard-stat-card">

            <div class="dashboard-stat-title">
                Tourist Spots
            </div>

            <div class="dashboard-stat-value">
                {{ number_format($totalSpots) }}
            </div>

        </div>


        <div class="dashboard-stat-card">

            <div class="dashboard-stat-title">
                Incident Reports
            </div>

            <div class="dashboard-stat-value">
                {{ number_format($totalIncidents) }}
            </div>

        </div>


        <div class="dashboard-stat-card">

            <div class="dashboard-stat-title">
                Crime Records
            </div>

            <div class="dashboard-stat-value">
                {{ number_format($totalCrimeRecords) }}
            </div>

        </div>


        <div class="dashboard-stat-card">

            <div class="dashboard-stat-title">
                Safety Alerts
            </div>

            <div class="dashboard-stat-value">
                {{ number_format($totalSafetyAlerts) }}
            </div>

        </div>


        <div class="dashboard-stat-card">

            <div class="dashboard-stat-title">
                Completed Trips
            </div>

            <div class="dashboard-stat-value">
                {{ number_format($completedTrips) }}
            </div>

        </div>

    </section>


    <!-- Main Dashboard Grid -->

    <div class="dashboard-main-grid">

        <!-- Recent Incident Reports -->

        <section class="dashboard-panel">

            <div class="dashboard-panel-header">

                <h2>
                    Recent Incident Reports
                </h2>

                <a
                    href="{{ route('admin.incident-reports.index') }}"
                    class="panel-link"
                >
                    View All
                </a>

            </div>


            <div class="dashboard-table-wrapper">

                <table class="dashboard-table">

                    <thead>

                        <tr>
                            <th>Incident</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>

                    </thead>


                    <tbody>

                        @forelse($recentIncidents as $incident)

                            <tr>

                                <td>

                                    <div class="incident-title">
                                        {{ $incident->title ?? 'Untitled Incident' }}
                                    </div>

                                    @if(!empty($incident->description))

                                        <div class="incident-description">

                                            {{
                                                \Illuminate\Support\Str::limit(
                                                    $incident->description,
                                                    70
                                                )
                                            }}

                                        </div>

                                    @endif

                                </td>


                                <td>

                                    @php

                                        $incidentStatus = strtolower(
                                            $incident->status ?? 'pending'
                                        );

                                    @endphp

                                    <span
                                        class="status-badge status-{{ $incidentStatus }}"
                                    >
                                        {{ $incident->status ?? 'Pending' }}
                                    </span>

                                </td>


                                <td>

                                    {{
                                        \Carbon\Carbon::parse(
                                            $incident->created_at
                                        )
                                        ->timezone('Asia/Manila')
                                        ->format('M d, Y')
                                    }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="3"
                                    class="dashboard-empty"
                                >
                                    No incident reports found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>


        <!-- Recent Activity -->

        <section class="dashboard-panel">

            <div class="dashboard-panel-header">

                <h2>
                    Recent Activity
                </h2>

            </div>


            <div class="activity-list">

                @forelse($recentTourists as $tourist)

                    <div class="activity-item">

                        <span class="activity-dot"></span>

                        <div>

                            <strong>
                                New Tourist Account
                            </strong>

                            <span>

                                {{
                                    trim(
                                        ($tourist->first_name ?? '') .
                                        ' ' .
                                        ($tourist->last_name ?? '')
                                    )
                                }}

                                joined CityPulse.

                            </span>

                        </div>

                    </div>

                @empty

                    <div class="activity-item">

                        <div class="dashboard-empty">
                            No recent activity.
                        </div>

                    </div>

                @endforelse

            </div>

        </section>

    </div>


    <!-- Recent Registrations -->

    <section class="dashboard-panel registrations-panel">

        <div class="dashboard-panel-header">

            <h2>
                Recent Tourist Registrations
            </h2>

            <a
                href="{{ route('admin.tourist-accounts.index') }}"
                class="panel-link"
            >
                View All
            </a>

        </div>


        <div class="registration-grid">

            @forelse($recentTourists as $tourist)

                @php

                    $fullName = trim(
                        ($tourist->first_name ?? '') .
                        ' ' .
                        ($tourist->last_name ?? '')
                    );

                    if ($fullName === '') {
                        $fullName = 'Tourist';
                    }

                    $initials =
                        strtoupper(
                            substr(
                                $tourist->first_name ?? 'T',
                                0,
                                1
                            ) .
                            substr(
                                $tourist->last_name ?? '',
                                0,
                                1
                            )
                        );

                @endphp


                <div class="registration-card">

                    <div class="registration-avatar">

                        @if(!empty($tourist->profile_image_url))

                            <img
                                src="{{ $tourist->profile_image_url }}"
                                alt="{{ $fullName }}"
                            >

                        @else

                            {{ $initials }}

                        @endif

                    </div>


                    <div class="registration-info">

                        <strong>
                            {{ $fullName }}
                        </strong>

                        <span>

                            {{
                                \Carbon\Carbon::parse(
                                    $tourist->created_at
                                )
                                ->timezone('Asia/Manila')
                                ->format('M d, Y')
                            }}

                        </span>

                    </div>

                </div>

            @empty

                <div class="dashboard-empty">
                    No recent tourist registrations.
                </div>

            @endforelse

        </div>

    </section>

@endsection