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

    <div class="spot-show-header">

        <div>

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


        <div class="spot-show-actions">

            <a
                href="{{ route('admin.tourist-spots.edit', $spot->tourist_id) }}"
                class="spot-show-edit"
            >
                Edit Spot
            </a>


            @if($spot->is_active)

                <form
                    method="POST"
                    action="{{ route('admin.tourist-spots.status', $spot->tourist_id) }}"
                    class="spot-show-status-form"
                    onsubmit="return confirm('Deactivate this tourist spot? Historical records will remain available.');"
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
                        class="spot-show-deactivate"
                    >
                        Deactivate
                    </button>

                </form>

            @else

                <form
                    method="POST"
                    action="{{ route('admin.tourist-spots.status', $spot->tourist_id) }}"
                    class="spot-show-status-form"
                    onsubmit="return confirm('Reactivate this tourist spot?');"
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
                        class="spot-show-reactivate"
                    >
                        Reactivate
                    </button>

                </form>

            @endif

        </div>

    </div>


    @if(session('success'))

        <div class="spot-show-success">
            {{ session('success') }}
        </div>

    @endif


    <section class="spot-overview">

        <div class="spot-overview-main">

            <div>

                <div class="spot-title-row">

                    <h2>
                        {{ $spot->name }}
                    </h2>

                    @if($spot->is_active)

                        <span class="spot-status spot-status-active">
                            Active
                        </span>

                    @else

                        <span class="spot-status spot-status-inactive">
                            Inactive
                        </span>

                    @endif

                </div>


                <div class="spot-overview-meta">

                    <span>
                        {{ $spot->category }}
                    </span>

                    @if(!empty($spot->address))

                        <span>
                            {{ $spot->address }}
                        </span>

                    @endif

                </div>

            </div>


            <div class="spot-id">

                <span>
                    Spot ID
                </span>

                <strong>
                    #{{ $spot->tourist_id }}
                </strong>

            </div>

        </div>

    </section>


    <div class="spot-details-grid">

        <div class="spot-details-column">

            <section class="spot-card">

                <div class="spot-card-header">
                    <h2>Information</h2>
                </div>


                <div class="spot-card-body">

                    <div class="spot-info-row">

                        <span class="spot-info-label">
                            Name
                        </span>

                        <span class="spot-info-value">
                            {{ $spot->name }}
                        </span>

                    </div>


                    <div class="spot-info-row">

                        <span class="spot-info-label">
                            Category
                        </span>

                        <span class="spot-info-value">
                            {{ $spot->category }}
                        </span>

                    </div>


                    <div class="spot-info-row">

                        <span class="spot-info-label">
                            Status
                        </span>

                        <span class="spot-info-value">

                            @if($spot->is_active)
                                Active
                            @else
                                Inactive
                            @endif

                        </span>

                    </div>


                    <div class="spot-info-row">

                        <span class="spot-info-label">
                            Opening Hours
                        </span>

                        <span class="spot-info-value">
                            {{ $spot->opening_hours ?? '—' }}
                        </span>

                    </div>


                    <div class="spot-info-row">

                        <span class="spot-info-label">
                            Created
                        </span>

                        <span class="spot-info-value">

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


            <section class="spot-card">

                <div class="spot-card-header">
                    <h2>Location</h2>
                </div>


                <div class="spot-card-body">

                    <div class="spot-info-row">

                        <span class="spot-info-label">
                            Address
                        </span>

                        <span class="spot-info-value">
                            {{ $spot->address ?? '—' }}
                        </span>

                    </div>


                    <div class="spot-info-row">

                        <span class="spot-info-label">
                            Latitude
                        </span>

                        <span class="spot-info-value">

                            @if($spot->latitude !== null)

                                {{ number_format($spot->latitude, 6) }}

                            @else

                                —

                            @endif

                        </span>

                    </div>


                    <div class="spot-info-row">

                        <span class="spot-info-label">
                            Longitude
                        </span>

                        <span class="spot-info-value">

                            @if($spot->longitude !== null)

                                {{ number_format($spot->longitude, 6) }}

                            @else

                                —

                            @endif

                        </span>

                    </div>

                </div>

            </section>


            @if(!empty($spot->image_url))

                <section class="spot-card">

                    <div class="spot-card-header">
                        <h2>Image</h2>
                    </div>

                    <div class="spot-image-container">

                        <img
                            src="{{ $spot->image_url }}"
                            alt="{{ $spot->name }}"
                            class="spot-image"
                        >

                    </div>

                </section>

            @endif

        </div>


        <div class="spot-details-column">

            <section class="spot-card spot-description-card">

                <div class="spot-card-header">
                    <h2>Description</h2>
                </div>

                <div class="spot-text-content">
                    <p>{{ $spot->description ?? '—' }}</p>
                </div>

            </section>


            <section class="spot-card spot-history-card">

                <div class="spot-card-header">
                    <h2>History</h2>
                </div>

                <div class="spot-text-content">
                    <p>{{ $spot->history ?? '—' }}</p>
                </div>

            </section>

        </div>

    </div>

@endsection