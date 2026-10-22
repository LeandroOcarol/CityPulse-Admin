@extends('admin.layouts.app')

@section('title', 'CityPulse | Live Tourist Map')
@section('body-class', 'live-tourist-map-page')

@push('styles')

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/pages/live-tourist-map.css') }}"
    >

@endpush


@section('content')

    <div class="live-map-header">

        <h1>
            Live Tourist Map
        </h1>


        <a
            href="{{ route('admin.live-tourist-map.index') }}"
            class="live-map-refresh"
        >
            Refresh Locations
        </a>

    </div>


    <section class="live-map-summary">

        <div class="live-map-summary-card">

            <span>
                Tracked Tourists
            </span>

            <strong>
                {{ number_format($totalTracked) }}
            </strong>

        </div>


        <div class="live-map-summary-card">

            <span>
                Recent Locations
            </span>

            <strong>
                {{ number_format($recentCount) }}
            </strong>

        </div>


        <div class="live-map-summary-card">

            <span>
                Stale Locations
            </span>

            <strong>
                {{ number_format($staleCount) }}
            </strong>

        </div>

    </section>


    <div class="live-map-layout">

        <section class="live-map-card">

            <div class="live-map-card-header">

                <div>

                    <h2>
                        Tourist Locations
                    </h2>

                    <span>
                        Latest recorded position of each tourist
                    </span>

                </div>

            </div>


            <div
                id="touristMap"
                class="live-map"
            ></div>

        </section>


        <section class="live-tourist-list-card">

            <div class="live-tourist-list-header">

                <h2>
                    Tracked Tourists
                </h2>

                <span>
                    {{ number_format($totalTracked) }} records
                </span>

            </div>


            <div class="live-tourist-list">

                @forelse($tourists as $tourist)

                    <div class="live-tourist-item">

                        <div class="live-tourist-main">

                            <div class="live-tourist-name">

                                <strong>
                                    {{ $tourist->full_name }}
                                </strong>

                                <span>
                                    {{ number_format($tourist->latitude, 6) }},
                                    {{ number_format($tourist->longitude, 6) }}
                                </span>

                            </div>


                            <span
                                class="
                                    live-location-status
                                    {{ $tourist->location_status === 'Recent'
                                        ? 'live-location-recent'
                                        : 'live-location-stale'
                                    }}
                                "
                            >
                                {{ $tourist->location_status }}
                            </span>

                        </div>


                        <div class="live-tourist-meta">

                            <span>
                                Account:
                                {{ $tourist->is_active ? 'Active' : 'Inactive' }}
                            </span>

                            <span>
                                Updated:
                                {{ $tourist->updated_at_manila }}
                            </span>

                        </div>

                    </div>

                @empty

                    <div class="live-tourist-empty">
                        No tourist locations available.
                    </div>

                @endforelse

            </div>

        </section>

    </div>

@endsection


@push('scripts')

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    ></script>


    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const tourists = @json($tourists);

            const map = L.map('touristMap', {
                zoomControl: true
            });


            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                {
                    maxZoom: 19,
                    attribution:
                        '&copy; OpenStreetMap contributors'
                }
            ).addTo(map);


            const bounds = [];


            tourists.forEach(function (tourist) {

                if (
                    tourist.latitude === null ||
                    tourist.longitude === null
                ) {
                    return;
                }


                const latitude =
                    parseFloat(tourist.latitude);

                const longitude =
                    parseFloat(tourist.longitude);


                const marker = L.marker([
                    latitude,
                    longitude
                ]).addTo(map);


                const accountStatus =
                    tourist.is_active
                        ? 'Active'
                        : 'Inactive';


                marker.bindPopup(`
                    <div class="map-popup">
                        <strong>${tourist.full_name}</strong>

                        <span>
                            ${latitude.toFixed(6)},
                            ${longitude.toFixed(6)}
                        </span>

                        <span>
                            Account: ${accountStatus}
                        </span>

                        <span>
                            Location: ${tourist.location_status}
                        </span>

                        <span>
                            Updated: ${tourist.updated_at_manila}
                        </span>
                    </div>
                `);


                bounds.push([
                    latitude,
                    longitude
                ]);

            });


            if (bounds.length > 0) {

                map.fitBounds(
                    bounds,
                    {
                        padding: [40, 40],
                        maxZoom: 15
                    }
                );

            } else {

                map.setView(
                    [10.3157, 123.8854],
                    13
                );

            }

        });

    </script>

@endpush