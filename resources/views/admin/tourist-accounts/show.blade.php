@extends('admin.layouts.app')

@section('title', 'CityPulse | Tourist Account')

@section('body-class', 'tourist-account-show-page')


@push('styles')

    <link
        rel="stylesheet"
        href="/css/pages/tourist-account-show.css"
    >

@endpush


@section('content')


    <!-- Page Header -->

    <div class="account-show-header">

        <a
            href="{{ route('admin.tourist-accounts.index') }}"
            class="back-link"
        >
            Back to Tourist Accounts
        </a>

        <h1>
            Tourist Account
        </h1>

    </div>



    <!-- Profile Overview -->

    <section class="account-profile-card">

        <div class="account-profile-main">

            <div class="account-profile-avatar">

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


            <div class="account-profile-info">

                <h2>

                    {{ $tourist->first_name ?? '' }}
                    {{ $tourist->last_name ?? '' }}

                </h2>


                <div class="account-profile-meta">

                    <span class="account-role-badge">
                        Tourist
                    </span>


                    @if(!empty($tourist->email))

                        <span class="account-profile-email">
                            {{ $tourist->email }}
                        </span>

                    @endif

                </div>

            </div>

        </div>


        <div class="account-id-block">

            <span class="account-id-label">
                Account ID
            </span>

            <span class="account-id-value">
                {{ $tourist->id }}
            </span>

        </div>

    </section>



    <!-- Activity Summary -->

    <section class="account-stats-grid">


        <div class="account-stat-card">

            <span class="account-stat-label">
                Total Trips
            </span>

            <strong class="account-stat-value">
                {{ number_format($tripCount ?? 0) }}
            </strong>

        </div>


        <div class="account-stat-card">

            <span class="account-stat-label">
                Completed Trips
            </span>

            <strong class="account-stat-value">
                {{ number_format($completedTrips ?? 0) }}
            </strong>

        </div>


        <div class="account-stat-card">

            <span class="account-stat-label">
                Reviews
            </span>

            <strong class="account-stat-value">
                {{ number_format($reviewCount ?? 0) }}
            </strong>

        </div>

    </section>



    <!-- Account Details -->

    <div class="account-details-grid">


        <!-- Personal Information -->

        <section class="account-detail-card">

            <div class="account-detail-header">

                <h2>
                    Personal Information
                </h2>

            </div>


            <div class="account-detail-body">

                <div class="account-detail-row">

                    <span class="account-detail-label">
                        First Name
                    </span>

                    <span class="account-detail-value">
                        {{ $tourist->first_name ?? '—' }}
                    </span>

                </div>


                <div class="account-detail-row">

                    <span class="account-detail-label">
                        Last Name
                    </span>

                    <span class="account-detail-value">
                        {{ $tourist->last_name ?? '—' }}
                    </span>

                </div>


                <div class="account-detail-row">

                    <span class="account-detail-label">
                        Email
                    </span>

                    <span class="account-detail-value">
                        {{ $tourist->email ?? '—' }}
                    </span>

                </div>


                <div class="account-detail-row">

                    <span class="account-detail-label">
                        Phone
                    </span>

                    <span class="account-detail-value">
                        {{ $tourist->phone_number ?? '—' }}
                    </span>

                </div>

            </div>

        </section>



        <!-- Account Information -->

        <section class="account-detail-card">

            <div class="account-detail-header">

                <h2>
                    Account Information
                </h2>

            </div>


            <div class="account-detail-body">

                <div class="account-detail-row">

                    <span class="account-detail-label">
                        Role
                    </span>

                    <span class="account-detail-value">

                        <span class="account-role-badge">
                            Tourist
                        </span>

                    </span>

                </div>


                <div class="account-detail-row">

                    <span class="account-detail-label">
                        Registered
                    </span>

                    <span class="account-detail-value">

                        @if(!empty($tourist->created_at))

                            {{
                                \Carbon\Carbon::parse(
                                    $tourist->created_at
                                )->format('F d, Y')
                            }}

                        @else

                            —

                        @endif

                    </span>

                </div>


                <div class="account-detail-row">

                    <span class="account-detail-label">
                        Last Sign In
                    </span>

                    <span class="account-detail-value">

                        @if(!empty($tourist->last_sign_in_at))

                            {{
                                \Carbon\Carbon::parse(
                                    $tourist->last_sign_in_at
                                )->format('M d, Y h:i A')
                            }}

                        @else

                            —

                        @endif

                    </span>

                </div>


                <div class="account-detail-row">

                    <span class="account-detail-label">
                        Authentication
                    </span>

                    <span class="account-detail-value">

                        <span class="account-auth-badge">
                            Supabase Auth
                        </span>

                    </span>

                </div>

            </div>

        </section>

    </div>

@endsection