<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>CityPulse | Admin Dashboard</title>

    <link rel="stylesheet" href="/css/variables.css">
    <link rel="stylesheet" href="/css/base.css">
    <link rel="stylesheet" href="/css/components.css">
    <link rel="stylesheet" href="/css/layout.css">
    <link rel="stylesheet" href="/css/pages/dashboard.css">
</head>

<body class="dashboard-page">


    <!-- =========================
         SIDEBAR
    ========================== -->

    <aside class="sidebar">

        <div class="sidebar-brand">

            <div class="brand-logo">
                C
            </div>

            <div>

                <div class="brand-name">
                    CityPulse
                </div>

                <div class="brand-subtitle">
                    Admin Dashboard
                </div>

            </div>

        </div>


        <nav class="sidebar-menu">


            <!-- OVERVIEW -->

            <div class="menu-title">
                Overview
            </div>

            <a
                href="{{ route('admin.dashboard') }}"
                class="menu-item active"
            >
                Dashboard
            </a>


            <!-- MANAGEMENT -->

            <div class="menu-title">
                Management
            </div>

            <a
                href="#"
                class="menu-item"
            >
                Tourist Accounts
            </a>

            <a
                href="#"
                class="menu-item"
            >
                Tourist Spots
            </a>

            <a
                href="#"
                class="menu-item"
            >
                Incident Reports
            </a>

            <a
                href="#"
                class="menu-item"
            >
                Crime Records
            </a>

            <a
                href="#"
                class="menu-item"
            >
                Safety Alerts
            </a>


            <!-- GIS -->

            <div class="menu-title">
                GIS & Analytics
            </div>

            <a
                href="#"
                class="menu-item"
            >
                Live Tourist Map
            </a>

            <a
                href="#"
                class="menu-item"
            >
                Safety Heat Map
            </a>

            <a
                href="#"
                class="menu-item"
            >
                Geofence Zones
            </a>

            <a
                href="#"
                class="menu-item"
            >
                Map Routes
            </a>

            <a
                href="#"
                class="menu-item"
            >
                Predictive Analytics
            </a>


            <!-- REPORTS -->

            <div class="menu-title">
                Reports
            </div>

            <a
                href="#"
                class="menu-item"
            >
                Ratings & Feedback
            </a>

            <a
                href="#"
                class="menu-item"
            >
                Reports
            </a>


            <!-- SYSTEM -->

            <div class="menu-title">
                System
            </div>

            <a
                href="#"
                class="menu-item"
            >
                Settings
            </a>


            <!-- LOGOUT -->

            <form
                method="POST"
                action="{{ route('admin.logout') }}"
            >

                @csrf

                <button
                    type="submit"
                    class="menu-item logout-item"
                >
                    Logout
                </button>

            </form>

        </nav>

    </aside>



    <!-- =========================
         MAIN
    ========================== -->

    <main class="main">


        <!-- TOPBAR -->

        <header class="topbar">

            <div class="search-box">

                <input
                    type="search"
                    placeholder="Search CityPulse..."
                    aria-label="Search CityPulse"
                >

            </div>


            <div class="admin-info">

                <div class="admin-avatar">

                    {{
                        strtoupper(
                            substr(
                                session(
                                    'admin_user.first_name',
                                    'A'
                                ),
                                0,
                                1
                            )
                        )
                    }}

                </div>


                <div>

                    <div class="admin-name">

                        {{ session('admin_user.first_name') }}
                        {{ session('admin_user.last_name') }}

                    </div>

                    <div class="admin-role">
                        Administrator
                    </div>

                </div>

            </div>

        </header>



        <!-- =========================
             CONTENT
        ========================== -->

        <div class="content">


            <!-- PAGE HEADER -->

            <div class="page-header">

                <h1>
                    Dashboard
                </h1>

                <p>
                    Monitor CityPulse tourism and safety information.
                </p>

            </div>



            <!-- QUICK ACTIONS -->

            <div class="quick-actions-label">
                Quick Actions
            </div>

            <div class="quick-actions">

                <a
                    href="#"
                    class="btn btn-primary"
                >
                    Add Tourist Spot
                </a>

                <a
                    href="#"
                    class="btn btn-warning"
                >
                    Add Crime Record
                </a>

                <a
                    href="#"
                    class="btn btn-info"
                >
                    Create Safety Alert
                </a>

                <a
                    href="#"
                    class="btn btn-dark"
                >
                    Generate Report
                </a>

            </div>



            <!-- =========================
                 SUMMARY CARDS
            ========================== -->

            <section class="stats-grid">


                <!-- TOURISTS -->

                <div class="card stat-card">

                    <div>

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

                </div>


                <!-- TOURIST SPOTS -->

                <div class="card stat-card">

                    <div>

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

                </div>


                <!-- INCIDENTS -->

                <div class="card stat-card">

                    <div>

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

                </div>


                <!-- CRIME RECORDS -->

                <div class="card stat-card">

                    <div>

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

                </div>


                <!-- SAFETY ALERTS -->

                <div class="card stat-card">

                    <div>

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

                </div>


                <!-- COMPLETED TRIPS -->

                <div class="card stat-card">

                    <div>

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

                </div>

            </section>



            <!-- =========================
                 INCIDENTS + SYSTEM
            ========================== -->

            <section class="dashboard-grid">


                <!-- LATEST INCIDENTS -->

                <div class="card panel">

                    <div class="panel-header">

                        <h3>
                            Latest Incident Reports
                        </h3>

                        <a
                            href="#"
                            class="view-link"
                        >
                            View All
                        </a>

                    </div>


                    @if($recentIncidents->count() > 0)

                        <table>

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

                                            <strong>
                                                Incident Report
                                            </strong>


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
                                                $status === 'completed'
                                                ||
                                                $status === 'resolved'
                                            )

                                                <span
                                                    class="badge badge-success"
                                                >
                                                    {{ ucfirst($status) }}
                                                </span>


                                            @elseif(
                                                $status === 'critical'
                                            )

                                                <span
                                                    class="badge badge-danger"
                                                >
                                                    Critical
                                                </span>


                                            @else

                                                <span
                                                    class="badge badge-warning"
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


                    @else

                        <div class="empty-state">
                            No incident reports available.
                        </div>

                    @endif

                </div>



                <!-- SYSTEM STATUS -->

                <div class="card panel">

                    <div class="panel-header">

                        <h3>
                            System Status
                        </h3>

                    </div>


                    <div class="activity-list">


                        <div class="activity-item">

                            <div class="activity-dot"></div>

                            <div>

                                <div class="activity-text">
                                    Admin Dashboard active.
                                </div>

                                <div class="activity-date">
                                    Current session
                                </div>

                            </div>

                        </div>


                        <div class="activity-item">

                            <div class="activity-dot"></div>

                            <div>

                                <div class="activity-text">
                                    Supabase database connected.
                                </div>

                                <div class="activity-date">
                                    Database
                                </div>

                            </div>

                        </div>


                        <div class="activity-item">

                            <div class="activity-dot"></div>

                            <div>

                                <div class="activity-text">
                                    Administrator access verified.
                                </div>

                                <div class="activity-date">
                                    Authentication
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </section>



            <!-- =========================
                 RECENT TOURISTS
            ========================== -->

            <section class="card panel tourists-panel">

                <div class="panel-header">

                    <h3>
                        Recent Tourist Registrations
                    </h3>

                    <a
                        href="#"
                        class="view-link"
                    >
                        View Directory
                    </a>

                </div>


                @if($recentTourists->count() > 0)

                    <div class="tourist-list">


                        @foreach(
                            $recentTourists
                            as $tourist
                        )

                            <div class="tourist-card">

                                <div class="tourist-avatar">

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


                                <div>

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

                                    <div class="tourist-meta">
                                        Registered tourist
                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>


                @else

                    <div class="empty-state">
                        No tourist registrations available.
                    </div>

                @endif

            </section>

        </div>

    </main>

</body>

</html>