@extends('admin.layouts.app')


@section('title', 'CityPulse | Admin Dashboard')


@section('body-class', 'dashboard-page')


@push('styles')

    <link
        rel="stylesheet"
        href="/css/pages/dashboard.css"
    >

@endpush


@section('content')


    <!-- DASHBOARD HEADING -->

    <div class="dashboard-heading">

        <h1>
            Dashboard
        </h1>

    </div>


    <!-- QUICK ACTIONS -->

    <section class="quick-actions-section">

        <div class="section-label">
            Quick Actions
        </div>


        <div class="quick-actions">

            <a
                href="#"
                class="dashboard-action primary"
            >
                Add Tourist Spot
            </a>


            <a
                href="#"
                class="dashboard-action warning"
            >
                Add Crime Record
            </a>


            <a
                href="#"
                class="dashboard-action alert"
            >
                Create Safety Alert
            </a>


            <a
                href="#"
                class="dashboard-action dark"
            >
                Generate Report
            </a>

        </div>

    </section>



    <!-- STATISTICS -->

    <section class="stats-grid">


        <div class="dashboard-stat-card">

            <div class="stat-card-top">

                <div class="stat-symbol">
                    TA
                </div>

            </div>

            <div class="stat-title">
                Total Tourists
            </div>

            <div class="stat-number">
                {{ number_format($totalTourists) }}
            </div>

            <div class="stat-description">
                Registered tourist accounts
            </div>

        </div>



        <div class="dashboard-stat-card">

            <div class="stat-card-top">

                <div class="stat-symbol">
                    TS
                </div>

            </div>

            <div class="stat-title">
                Tourist Spots
            </div>

            <div class="stat-number">
                {{ number_format($totalSpots) }}
            </div>

            <div class="stat-description">
                Registered destinations
            </div>

        </div>



        <div class="dashboard-stat-card">

            <div class="stat-card-top">

                <div class="stat-symbol danger">
                    IR
                </div>

            </div>

            <div class="stat-title">
                Incident Reports
            </div>

            <div class="stat-number">
                {{ number_format($totalIncidents) }}
            </div>

            <div class="stat-description">
                Submitted incident reports
            </div>

        </div>



        <div class="dashboard-stat-card">

            <div class="stat-card-top">

                <div class="stat-symbol warning">
                    CR
                </div>

            </div>

            <div class="stat-title">
                Crime Records
            </div>

            <div class="stat-number">
                {{ number_format($totalCrimeRecords) }}
            </div>

            <div class="stat-description">
                Recorded crime data
            </div>

        </div>



        <div class="dashboard-stat-card">

            <div class="stat-card-top">

                <div class="stat-symbol danger">
                    SA
                </div>

            </div>

            <div class="stat-title">
                Safety Alerts
            </div>

            <div class="stat-number">
                {{ number_format($totalSafetyAlerts) }}
            </div>

            <div class="stat-description">
                Safety alert records
            </div>

        </div>



        <div class="dashboard-stat-card">

            <div class="stat-card-top">

                <div class="stat-symbol">
                    CT
                </div>

            </div>

            <div class="stat-title">
                Completed Trips
            </div>

            <div class="stat-number">
                {{ number_format($completedTrips) }}
            </div>

            <div class="stat-description">
                Completed tourist trips
            </div>

        </div>

    </section>



    <!-- LOWER SECTION -->

    <section class="dashboard-lower-grid">


        <!-- INCIDENT REPORTS -->

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


            @if($recentIncidents->count() > 0)

                <div class="dashboard-table-wrapper">

                    <table class="dashboard-table">

                        <thead>

                            <tr>

                                <th>
                                    Incident
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Date
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach(
                                $recentIncidents
                                as $incident
                            )

                                @php

                                    $status =
                                        strtolower(
                                            $incident->status
                                            ?? 'pending'
                                        );

                                @endphp


                                <tr>

                                    <td>

                                        <div class="incident-title">
                                            Incident Report
                                        </div>


                                        @if(
                                            isset(
                                                $incident->description
                                            )
                                        )

                                            <div
                                                class="incident-description"
                                            >

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

                                        @if(
                                            $status === 'resolved'
                                            ||
                                            $status === 'completed'
                                        )

                                            <span
                                                class="status-pill resolved"
                                            >
                                                {{ ucfirst($status) }}
                                            </span>


                                        @elseif(
                                            $status === 'critical'
                                        )

                                            <span
                                                class="status-pill critical"
                                            >
                                                Critical
                                            </span>


                                        @else

                                            <span
                                                class="status-pill pending"
                                            >
                                                {{ ucfirst($status) }}
                                            </span>

                                        @endif

                                    </td>


                                    <td>

                                        @if(
                                            isset(
                                                $incident->created_at
                                            )
                                        )

                                            {{
                                                \Carbon\Carbon::parse(
                                                    $incident->created_at
                                                )->format(
                                                    'M d, Y'
                                                )
                                            }}

                                        @else

                                            —

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


            @else

                <div class="dashboard-empty-state">
                    No incident reports available.
                </div>

            @endif

        </div>



        <!-- RECENT ACTIVITIES -->

        <div class="dashboard-panel">

            <div class="dashboard-panel-header">

                <h2>
                    Recent Activities
                </h2>

            </div>


            <div class="activity-list">


                <div class="activity-row">

                    <div class="activity-indicator"></div>

                    <div>

                        <div class="activity-title">
                            Admin dashboard accessed
                        </div>

                        <div class="activity-subtitle">
                            Current session
                        </div>

                    </div>

                </div>


                <div class="activity-row">

                    <div class="activity-indicator"></div>

                    <div>

                        <div class="activity-title">
                            Supabase database connected
                        </div>

                        <div class="activity-subtitle">
                            System connection active
                        </div>

                    </div>

                </div>


                <div class="activity-row">

                    <div class="activity-indicator"></div>

                    <div>

                        <div class="activity-title">
                            Administrator access verified
                        </div>

                        <div class="activity-subtitle">
                            Authentication successful
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>



    <!-- RECENT TOURIST REGISTRATIONS -->

    <section
        class="dashboard-panel tourist-registration-panel"
    >

        <div class="dashboard-panel-header">

            <h2>
                Recent Tourist Registrations
            </h2>

            <a
                href="#"
                class="panel-link"
            >
                View Directory
            </a>

        </div>


        @if($recentTourists->count() > 0)

            <div class="recent-tourists">

                @foreach(
                    $recentTourists
                    as $tourist
                )

                    <div class="recent-tourist-card">

                        <div class="tourist-initial">

                            {{
                                strtoupper(
                                    substr(
                                        $tourist->first_name
                                        ?? 'T',
                                        0,
                                        1
                                    )
                                )
                            }}

                        </div>


                        <div class="tourist-info">

                            <div class="tourist-name">

                                {{
                                    $tourist->first_name
                                    ?? 'Tourist'
                                }}

                                {{
                                    $tourist->last_name
                                    ?? ''
                                }}

                            </div>

                            <div class="tourist-role">
                                Registered tourist
                            </div>

                        </div>

                    </div>

                @endforeach

            </div>


        @else

            <div class="dashboard-empty-state">
                No recent tourist registrations.
            </div>

        @endif

    </section>


@endsection