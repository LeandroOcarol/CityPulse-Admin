@extends('admin.layouts.app')

@section('title', 'CityPulse | Safety Alert')
@section('body-class', 'safety-alert-show-page')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/pages/safety-alert-show.css') }}"
    >
@endpush

@section('content')

    @php

        $levelClass = strtolower(
            $alert->alert_level
        );

        $creatorName = trim(
            ($alert->creator_first_name ?? '') . ' ' .
            ($alert->creator_last_name ?? '')
        );

        if ($creatorName === '') {
            $creatorName = 'System / Not recorded';
        }

    @endphp


    <div class="alert-show-header">

        <div>

            <a
                href="{{ route('admin.safety-alerts.index') }}"
                class="alert-show-back"
            >
                Back to Safety Alerts
            </a>

            <h1>
                Safety Alert
            </h1>

        </div>


        <div class="alert-show-actions">

            <a
                href="{{ route('admin.safety-alerts.edit', $alert->alert_id) }}"
                class="alert-show-edit"
            >
                Edit Alert
            </a>


            @if($alert->is_active)

                <form
                    method="POST"
                    action="{{ route('admin.safety-alerts.status', $alert->alert_id) }}"
                    onsubmit="return confirm('Deactivate this safety alert?');"
                >
                    @csrf
                    @method('PATCH')

                    <input
                        type="hidden"
                        name="is_active"
                        value="0"
                    >

                    <button class="alert-show-deactivate">
                        Deactivate
                    </button>

                </form>

            @else

                <form
                    method="POST"
                    action="{{ route('admin.safety-alerts.status', $alert->alert_id) }}"
                    onsubmit="return confirm('Reactivate this safety alert?');"
                >
                    @csrf
                    @method('PATCH')

                    <input
                        type="hidden"
                        name="is_active"
                        value="1"
                    >

                    <button class="alert-show-reactivate">
                        Reactivate
                    </button>

                </form>

            @endif


            <form
                method="POST"
                action="{{ route('admin.safety-alerts.destroy', $alert->alert_id) }}"
                onsubmit="return confirm('Are you sure you want to permanently delete this safety alert?');"
            >
                @csrf
                @method('DELETE')

                <button class="alert-show-delete">
                    Delete Alert
                </button>

            </form>

        </div>

    </div>


    @if(session('success'))

        <div class="alert-show-success">
            {{ session('success') }}
        </div>

    @endif


    <section class="alert-show-overview">

        <div>

            <h2>
                {{ $alert->title }}
            </h2>

            <div class="alert-show-badges">

                <span class="alert-show-level alert-show-level-{{ $levelClass }}">
                    {{ $alert->alert_level }}
                </span>


                @if($alert->is_active)

                    <span class="alert-show-status alert-show-status-active">
                        Active
                    </span>

                @else

                    <span class="alert-show-status alert-show-status-inactive">
                        Inactive
                    </span>

                @endif

            </div>

        </div>


        <div class="alert-show-id">

            <span>Alert ID</span>

            <strong>
                #{{ $alert->alert_id }}
            </strong>

        </div>

    </section>


    <div class="alert-show-grid">

        <div class="alert-show-column">

            <section class="alert-show-card">

                <div class="alert-show-card-header">
                    <h2>Alert Information</h2>
                </div>


                <div class="alert-show-card-body">

                    <div class="alert-show-row">
                        <span>Alert Level</span>
                        <strong>{{ $alert->alert_level }}</strong>
                    </div>

                    <div class="alert-show-row">
                        <span>Status</span>
                        <strong>
                            {{ $alert->is_active ? 'Active' : 'Inactive' }}
                        </strong>
                    </div>

                    <div class="alert-show-row">
                        <span>Created By</span>
                        <strong>{{ $creatorName }}</strong>
                    </div>

                    <div class="alert-show-row">

                        <span>Created</span>

                        <strong>
                            {{
                                \Carbon\Carbon::parse(
                                    $alert->created_at
                                )
                                ->timezone('Asia/Manila')
                                ->format('F d, Y h:i A')
                            }}
                        </strong>

                    </div>

                </div>

            </section>


            <section class="alert-show-card">

                <div class="alert-show-card-header">
                    <h2>Schedule</h2>
                </div>


                <div class="alert-show-card-body">

                    <div class="alert-show-row">

                        <span>Starts At</span>

                        <strong>
                            {{
                                \Carbon\Carbon::parse(
                                    $alert->starts_at
                                )
                                ->timezone('Asia/Manila')
                                ->format('F d, Y h:i A')
                            }}
                        </strong>

                    </div>


                    <div class="alert-show-row">

                        <span>Ends At</span>

                        <strong>

                            @if(!empty($alert->ends_at))

                                {{
                                    \Carbon\Carbon::parse(
                                        $alert->ends_at
                                    )
                                    ->timezone('Asia/Manila')
                                    ->format('F d, Y h:i A')
                                }}

                            @else

                                No scheduled end

                            @endif

                        </strong>

                    </div>

                </div>

            </section>

        </div>


        <div class="alert-show-column">

            <section class="alert-show-card">

                <div class="alert-show-card-header">
                    <h2>Geofence Zone</h2>
                </div>


                <div class="alert-show-card-body">

                    <div class="alert-show-row">
                        <span>Zone</span>
                        <strong>{{ $alert->zone_name ?? 'Unknown' }}</strong>
                    </div>

                    <div class="alert-show-row">
                        <span>Zone Type</span>
                        <strong>{{ $alert->zone_type ?? '—' }}</strong>
                    </div>

                    <div class="alert-show-row">

                        <span>Radius</span>

                        <strong>

                            @if($alert->radius_meters !== null)

                                {{ number_format($alert->radius_meters) }} meters

                            @else

                                —

                            @endif

                        </strong>

                    </div>

                </div>

            </section>


            <section class="alert-show-card alert-message-card">

                <div class="alert-show-card-header">
                    <h2>Alert Message</h2>
                </div>

                <div class="alert-show-message">
                    <p>{{ $alert->message }}</p>
                </div>

            </section>

        </div>

    </div>

@endsection