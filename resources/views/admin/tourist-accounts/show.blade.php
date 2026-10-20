@extends('admin.layouts.app')

@section('title', 'CityPulse | Tourist Account Details')

@section('body-class', 'tourist-account-details-page')

@push('styles')
    <link rel="stylesheet" href="/css/pages/tourist-account-show.css">
@endpush

@section('content')

    <!-- PAGE HEADER -->

    <div class="account-details-header">

        <div>

            <a
                href="{{ route('admin.tourist-accounts.index') }}"
                class="back-link"
            >
                Back to Tourist Accounts
            </a>

            <h1>
                Tourist Account Details
            </h1>

        </div>

    </div>


    <!-- PROFILE OVERVIEW -->

    <section class="profile-overview-card">

        <div class="profile-main">

            <div class="profile-large-avatar">

                @if(!empty($tourist->profile_image_url))

                    <img
                        src="{{ $tourist->profile_image_url }}"
                        alt=""
                    >

                @else

                    <span>
                        {{
                            strtoupper(
                                substr(
                                    $tourist->first_name ?? 'T',
                                    0,
                                    1
                                )
                            )
                        }}
                    </span>

                @endif

            </div>


            <div class="profile-main-info">

                <h2>
                    {{ $tourist->first_name ?? 'Tourist' }}
                    {{ $tourist->last_name ?? '' }}
                </h2>

                <div class="profile-role">
                    Tourist
                </div>

                <p>
                    Registered CityPulse tourist account
                </p>

            </div>

        </div>


        <div class="account-id-area">

            <span class="account-id-label">
                Account ID
            </span>

            <span class="account-id-value">
                {{ $tourist->id }}
            </span>

        </div>

    </section>


    <!-- ACCOUNT STATISTICS -->

    <section class="account-statistics-grid">

        <div class="account-stat-card">

            <div class="account-stat-label">
                Total Trips
            </div>

            <div class="account-stat-number">
                {{ number_format($tripCount) }}
            </div>

        </div>


        <div class="account-stat-card">

            <div class="account-stat-label">
                Completed Trips
            </div>

            <div class="account-stat-number">
                {{ number_format($completedTrips) }}
            </div>

        </div>


        <div class="account-stat-card">

            <div class="account-stat-label">
                Reviews Submitted
            </div>

            <div class="account-stat-number">
                {{ number_format($reviewCount) }}
            </div>

        </div>

    </section>


    <!-- DETAILS GRID -->

    <section class="account-details-grid">


        <!-- PERSONAL INFORMATION -->

        <div class="details-panel">

            <div class="details-panel-header">

                <h2>
                    Personal Information
                </h2>

            </div>


            <div class="details-content">

                <div class="detail-row">

                    <div class="detail-label">
                        First Name
                    </div>

                    <div class="detail-value">
                        {{ $tourist->first_name ?? 'Not provided' }}
                    </div>

                </div>


                <div class="detail-row">

                    <div class="detail-label">
                        Last Name
                    </div>

                    <div class="detail-value">
                        {{ $tourist->last_name ?? 'Not provided' }}
                    </div>

                </div>


                <div class="detail-row">

                    <div class="detail-label">
                        Email Address
                    </div>

                    <div class="detail-value">
                        {{ $tourist->email ?? 'Not available' }}
                    </div>

                </div>


                <div class="detail-row">

                    <div class="detail-label">
                        Contact Number
                    </div>

                    <div class="detail-value">
                        {{ $tourist->phone_number ?? 'Not provided' }}
                    </div>

                </div>

            </div>

        </div>


        <!-- ACCOUNT INFORMATION -->

        <div class="details-panel">

            <div class="details-panel-header">

                <h2>
                    Account Information
                </h2>

            </div>


            <div class="details-content">

                <div class="detail-row">

                    <div class="detail-label">
                        Account Type
                    </div>

                    <div class="detail-value">

                        <span class="account-role-badge">
                            Tourist
                        </span>

                    </div>

                </div>


                <div class="detail-row">

                    <div class="detail-label">
                        Date Registered
                    </div>

                    <div class="detail-value">

                        @if($tourist->created_at)

                            {{
                                \Carbon\Carbon::parse(
                                    $tourist->created_at
                                )->format('F d, Y')
                            }}

                        @else

                            Not available

                        @endif

                    </div>

                </div>


                <div class="detail-row">

                    <div class="detail-label">
                        Last Sign In
                    </div>

                    <div class="detail-value">

                        @if($tourist->last_sign_in_at)

                            {{
                                \Carbon\Carbon::parse(
                                    $tourist->last_sign_in_at
                                )->format('M d, Y h:i A')
                            }}

                        @else

                            No sign-in record

                        @endif

                    </div>

                </div>


                <div class="detail-row">

                    <div class="detail-label">
                        Authentication
                    </div>

                    <div class="detail-value">

                        <span class="verification-badge">
                            Supabase Auth
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- INFORMATION NOTICE -->

    <section class="account-notice">

        <div>

            <h3>
                Account Information
            </h3>

            <p>
                This information is synchronized with the same
                CityPulse account used by the tourist mobile application.
            </p>

        </div>

    </section>

@endsection