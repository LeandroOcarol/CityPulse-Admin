@extends('admin.layouts.app')

@section('title', 'CityPulse | Dashboard')
@section('body-class', 'dashboard-page')

@push('styles')
    <link rel="stylesheet" href="/css/pages/dashboard.css">
@endpush

@section('content')

    <!-- Page Header -->

    <div class="dashboard-header">
        <h1>Dashboard</h1>
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
            href="#"
            class="action-button action-button-orange"
        >
            Add Crime Record
        </a>

        <a
            href="#"
            class="action-button action-button-green"
        >
            Create Safety Alert
        </a>

        <a
            href="#"
            class="action-button action-button-dark"
        >
            Generate Report
        </a>

    </section>


    <!-- Summary -->

    <section class="dashboard-stats">

        <div class="dashboard-stat-card">

            <span class="dashboard-stat-title">
                Total Tourists
            </span>

            <strong class="dashboard-stat-value">
                {{ number_format($totalTourists ?? 0) }}
            </strong>

        </div>


        <div class="dashboard-stat-card">

            <span class="dashboard-stat-title">
                Tourist Spots
            </span>

            <strong class="dashboard-stat-value">
                {{ number_format($totalSpots ?? 0) }}
            </strong>

        </div>


        <div class="dashboard-stat-card">

            <span class="dashboard-stat-title">
                Incident Reports
            </span>

            <strong class="dashboard-stat-value">
                {{ number_format($totalIncidents ?? 0) }}
            </strong>

        </div>


        <div class="dashboard-stat-card">

            <span class="dashboard-stat-title">
                Crime Records
            </span>

            <strong class="dashboard-stat-value">
                {{ number_format($totalCrimeRecords ?? 0) }}
            </strong>

        </div>


        <div class="dashboard-stat-card">

            <span class="dashboard-stat-title">
                Safety Alerts
            </span>

            <strong class="dashboard-stat-value">
                {{ number_format($totalSafetyAlerts ?? 0) }}
            </strong>

        </div>


        <div class="dashboard-stat-card">

            <span class="dashboard-stat-title">
                Completed Trips
            </span>

            <strong class="dashboard-stat-value">
                {{ number_format($completedTrips ?? 0) }}
            </strong>

        </div>

    </section>


    <!-- Main Grid -->

    <section class="dashboard-main-grid">


        <!-- Latest Incident Reports -->

        <div class="dashboard-panel">

            <div class="dashboard-panel-header">

                <h2>
                    Latest Incident Reports
                </h2>

                <a
                    href="#"
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

                        @forelse(($recentIncidents ?? []) as $incident)

                            <tr>

                                <td>

                                    <div class="incident-title">
                                        Incident Report
                                    </div>

                                    @if(!empty($incident->description))

                                        <div class="incident-description">

                                            {{
                                                \Illuminate\Support\Str::limit(
                                                    $incident->description,
                                                    45
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
                                        {{ ucfirst($incidentStatus) }}
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

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="3"
                                    class="dashboard-empty"
                                >
                                    No incident reports.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        <!-- Recent Activities -->

        <div class="dashboard-panel">

            <div class="dashboard-panel-header">

                <h2>
                    Recent Activities
                </h2>

            </div>


            <div class="activity-list">

                <div class="activity-item">

                    <span class="activity-dot"></span>

                    <div>

                        <strong>
                            Admin dashboard accessed
                        </strong>

                        <span>
                            Current session
                        </span>

                    </div>

                </div>


                <div class="activity-item">

                    <span class="activity-dot"></span>

                    <div>

                        <strong>
                            Supabase connected
                        </strong>

                        <span>
                            Database active
                        </span>

                    </div>

                </div>


                <div class="activity-item">

                    <span class="activity-dot"></span>

                    <div>

                        <strong>
                            Admin access verified
                        </strong>

                        <span>
                            Authentication successful
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- Recent Tourist Registrations -->

    <section class="dashboard-panel registrations-panel">

        <div class="dashboard-panel-header">

            <h2>
                Recent Tourist Registrations
            </h2>

            <a
                href="{{ route('admin.tourist-accounts.index') }}"
                class="panel-link"
            >
                View Directory
            </a>

        </div>


        <div class="registration-grid">

            @forelse(($recentTourists ?? []) as $tourist)

                <div class="registration-card">

                    <div class="registration-avatar">

                        @if(!empty($tourist->profile_image_url))

                            <img
                                src="{{ $tourist->profile_image_url }}"
                                alt=""
                            >

                        @else

                            {{
                                strtoupper(
                                    substr(
                                        $tourist->first_name ?? 'T',
                                        0,
                                        1
                                    )
                                )
                            }}

                        @endif

                    </div>


                    <div class="registration-info">

                        <strong>

                            {{
                                trim(
                                    ($tourist->first_name ?? '') .
                                    ' ' .
                                    ($tourist->last_name ?? '')
                                )
                            }}

                        </strong>

                        <span>
                            Tourist
                        </span>

                    </div>

                </div>

            @empty

                <div class="dashboard-empty">
                    No recent registrations.
                </div>

            @endforelse

        </div>

    </section>

@endsection