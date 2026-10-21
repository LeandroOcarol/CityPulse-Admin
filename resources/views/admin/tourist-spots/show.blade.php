@extends('admin.layouts.app')

@section('title', 'CityPulse | Tourist Spot')
@section('body-class', 'tourist-spot-show-page')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/pages/tourist-spot-show.css') }}"
    >
@endpush

@section('content')

    <!-- Page Header -->

    <div class="spot-show-header">

        <a
            href="{{ route('admin.tourist-spots.index') }}"
            class="spot-show-back"
        >
            Back to Tourist Spots
        </a>

        <h1>
            Tourist Spot
        </h1>

    </div>


    <!-- Spot Overview -->

    <section class="spot-overview-card">

        <div class="spot-overview-image">

            @if(!empty($spot->image_url))

                <img
                    src="{{ $spot->image_url }}"
                    alt="{{ $spot->name }}"
                >

            @else

                <span>
                    {{
                        strtoupper(
                            substr(
                                $spot->name ?? 'S',
                                0,
                                1
                            )
                        )
                    }}
                </span>

            @endif

        </div>


        <div class="spot-overview-info">

            <h2>
                {{ $spot->name }}
            </h2>

            <div class="spot-overview-meta">

                <span class="spot-show-category">
                    {{ $spot->category }}
                </span>

                @if(!empty($spot->address))

                    <span class="spot-show-address">
                        {{ $spot->address }}
                    </span>

                @endif

            </div>

        </div>

    </section>


    <!-- Details Grid -->

    <div class="spot-show-grid">


        <!-- Left Column -->

        <div class="spot-show-column spot-show-left">


            <!-- Information -->

            <section class="spot-show-card">

                <div class="spot-show-card-header">
                    <h2>Information</h2>
                </div>


                <div class="spot-show-card-body">

                    <div class="spot-detail-row">

                        <span class="spot-detail-label">
                            Name
                        </span>

                        <span class="spot-detail-value">
                            {{ $spot->name }}
                        </span>

                    </div>


                    <div class="spot-detail-row">

                        <span class="spot-detail-label">
                            Category
                        </span>

                        <span class="spot-detail-value">
                            {{ $spot->category }}
                        </span>

                    </div>


                    <div class="spot-detail-row">

                        <span class="spot-detail-label">
                            Address
                        </span>

                        <span class="spot-detail-value">
                            {{ $spot->address ?? '—' }}
                        </span>

                    </div>


                    <div class="spot-detail-row">

                        <span class="spot-detail-label">
                            Hours
                        </span>

                        <span class="spot-detail-value">
                            {{ $spot->opening_hours ?? '—' }}
                        </span>

                    </div>


                    <div class="spot-detail-row">

                        <span class="spot-detail-label">
                            Added
                        </span>

                        <span class="spot-detail-value">

                            @if(!empty($spot->created_at))

                                {{
                                    \Carbon\Carbon::parse(
                                        $spot->created_at
                                    )->format('F d, Y')
                                }}

                            @else

                                —

                            @endif

                        </span>

                    </div>

                </div>

            </section>


            <!-- Location -->

            <section class="spot-show-card">

                <div class="spot-show-card-header">
                    <h2>Location</h2>
                </div>


                <div class="spot-show-card-body">

                    <div class="spot-detail-row">

                        <span class="spot-detail-label">
                            Latitude
                        </span>

                        <span class="spot-detail-value">

                            @if($spot->latitude !== null)

                                {{ number_format($spot->latitude, 6) }}

                            @else

                                —

                            @endif

                        </span>

                    </div>


                    <div class="spot-detail-row">

                        <span class="spot-detail-label">
                            Longitude
                        </span>

                        <span class="spot-detail-value">

                            @if($spot->longitude !== null)

                                {{ number_format($spot->longitude, 6) }}

                            @else

                                —

                            @endif

                        </span>

                    </div>

                </div>

            </section>

        </div>


        <!-- Right Column -->

        <div class="spot-show-column spot-show-right">


            <!-- Description -->

            <section class="spot-show-card spot-description-card">

                <div class="spot-show-card-header">
                    <h2>Description</h2>
                </div>

                <div class="spot-text-content">
                    <p>{{ $spot->description ?? '—' }}</p>
                </div>

            </section>


            <!-- History -->

            <section class="spot-show-card spot-history-card">

                <div class="spot-show-card-header">
                    <h2>History</h2>
                </div>

                <div class="spot-text-content">
                    <p>{{ $spot->history ?? '—' }}</p>
                </div>

            </section>

        </div>

    </div>

@endsection