@extends('admin.layouts.app')

@section('title', 'CityPulse | Tourist Account')
@section('body-class', 'tourist-account-show-page')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/pages/tourist-account-show.css') }}"
    >
@endpush

@section('content')

    @php

        $fullName = trim(
            ($tourist->first_name ?? '') . ' ' .
            ($tourist->last_name ?? '')
        );

        if ($fullName === '') {
            $fullName = 'Unnamed Tourist';
        }

        $initials =
            strtoupper(
                mb_substr(
                    $tourist->first_name ?? 'T',
                    0,
                    1
                )
                .
                mb_substr(
                    $tourist->last_name ?? '',
                    0,
                    1
                )
            );

    @endphp


    <div class="tourist-show-header">

        <div>

            <a
                href="{{ route('admin.tourist-accounts.index') }}"
                class="tourist-show-back"
            >
                Back to Tourist Accounts
            </a>

            <h1>
                Tourist Account
            </h1>

        </div>


        @if($tourist->is_active)

            <form
                method="POST"
                action="{{ route('admin.tourist-accounts.status', $tourist->id) }}"
                class="tourist-show-status-form"
                onsubmit="return confirm('Deactivate this tourist account? Account history and related records will remain stored.');"
            >
                @csrf
                @method('PATCH')

                <input
                    type="hidden"
                    name="is_active"
                    value="0"
                >

                <button
                    type="submit"
                    class="tourist-show-deactivate"
                >
                    Deactivate Account
                </button>

            </form>

        @else

            <form
                method="POST"
                action="{{ route('admin.tourist-accounts.status', $tourist->id) }}"
                class="tourist-show-status-form"
                onsubmit="return confirm('Reactivate this tourist account?');"
            >
                @csrf
                @method('PATCH')

                <input
                    type="hidden"
                    name="is_active"
                    value="1"
                >

                <button
                    type="submit"
                    class="tourist-show-reactivate"
                >
                    Reactivate Account
                </button>

            </form>

        @endif

    </div>


    @if(session('success'))

        <div class="tourist-show-success">
            {{ session('success') }}
        </div>

    @endif


    <section class="tourist-profile-overview">

        <div class="tourist-profile-main">

            <div class="tourist-profile-avatar">

                @if(!empty($tourist->profile_image_url))

                    <img
                        src="{{ $tourist->profile_image_url }}"
                        alt="{{ $fullName }}"
                    >

                @else

                    <span>
                        {{ $initials }}
                    </span>

                @endif

            </div>


            <div class="tourist-profile-info">

                <div class="tourist-profile-name-row">

                    <h2>
                        {{ $fullName }}
                    </h2>

                    @if($tourist->is_active)

                        <span class="tourist-show-status tourist-show-status-active">
                            Active
                        </span>

                    @else

                        <span class="tourist-show-status tourist-show-status-inactive">
                            Inactive
                        </span>

                    @endif

                </div>


                <span class="tourist-profile-role">
                    Tourist
                </span>

                @if(!empty($tourist->email))

                    <span class="tourist-profile-email">
                        {{ $tourist->email }}
                    </span>

                @endif

            </div>

        </div>


        <div class="tourist-profile-id">

            <span>
                User ID
            </span>

            <strong>
                {{ $tourist->id }}
            </strong>

        </div>

    </section>


    <section class="tourist-activity-grid">

        <div class="tourist-activity-card">

            <span>
                Total Trips
            </span>

            <strong>
                {{ number_format($tripCount) }}
            </strong>

        </div>


        <div class="tourist-activity-card">

            <span>
                Completed Trips
            </span>

            <strong>
                {{ number_format($completedTrips) }}
            </strong>

        </div>


        <div class="tourist-activity-card">

            <span>
                Reviews
            </span>

            <strong>
                {{ number_format($reviewCount) }}
            </strong>

        </div>


        <div class="tourist-activity-card">

            <span>
                Incident Reports
            </span>

            <strong>
                {{ number_format($incidentCount) }}
            </strong>

        </div>

    </section>


    <div class="tourist-details-grid">

        <section class="tourist-detail-card">

            <div class="tourist-detail-header">
                <h2>Personal Information</h2>
            </div>


            <div class="tourist-detail-body">

                <div class="tourist-detail-row">

                    <span class="tourist-detail-label">
                        First Name
                    </span>

                    <span class="tourist-detail-value">
                        {{ $tourist->first_name ?? '—' }}
                    </span>

                </div>


                <div class="tourist-detail-row">

                    <span class="tourist-detail-label">
                        Last Name
                    </span>

                    <span class="tourist-detail-value">
                        {{ $tourist->last_name ?? '—' }}
                    </span>

                </div>


                <div class="tourist-detail-row">

                    <span class="tourist-detail-label">
                        Phone Number
                    </span>

                    <span class="tourist-detail-value">
                        {{ $tourist->phone_number ?? '—' }}
                    </span>

                </div>

            </div>

        </section>


        <section class="tourist-detail-card">

            <div class="tourist-detail-header">
                <h2>Account Information</h2>
            </div>


            <div class="tourist-detail-body">

                <div class="tourist-detail-row">

                    <span class="tourist-detail-label">
                        Email
                    </span>

                    <span class="tourist-detail-value">
                        {{ $tourist->email ?? '—' }}
                    </span>

                </div>


                <div class="tourist-detail-row">

                    <span class="tourist-detail-label">
                        Status
                    </span>

                    <span class="tourist-detail-value">

                        @if($tourist->is_active)
                            Active
                        @else
                            Inactive
                        @endif

                    </span>

                </div>


                <div class="tourist-detail-row">

                    <span class="tourist-detail-label">
                        Joined
                    </span>

                    <span class="tourist-detail-value">

                        @if(!empty($tourist->created_at))

                            {{
                                \Carbon\Carbon::parse(
                                    $tourist->created_at
                                )->format('F d, Y h:i A')
                            }}

                        @else

                            —

                        @endif

                    </span>

                </div>


                <div class="tourist-detail-row">

                    <span class="tourist-detail-label">
                        Last Sign In
                    </span>

                    <span class="tourist-detail-value">

                        @if(!empty($tourist->last_sign_in_at))

                            {{
                                \Carbon\Carbon::parse(
                                    $tourist->last_sign_in_at
                                )->format('F d, Y h:i A')
                            }}

                        @else

                            No sign-in recorded
                        @endif

                    </span>

                </div>

            </div>

        </section>

    </div>

@endsection