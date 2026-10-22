@extends('admin.layouts.app')

@section('title', 'CityPulse | Live Tourist Map')
@section('body-class', 'live-tourist-map-page')

@push('styles')

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


    <section class="live-map-legend">

        <div class="live-map-legend-item">

            <span
                class="
                    live-map-legend-dot
                    legend-tourist-recent
                "
            ></span>

            Recent Tourist

        </div>


        <div class="live-map-legend-item">

            <span
                class="
                    live-map-legend-dot
                    legend-tourist-stale
                "
            ></span>

            Stale Tourist

        </div>


        <div class="live-map-legend-item">

            <span
                class="
                    live-map-legend-dot
                    legend-tourist-spot
                "
            ></span>

            Tourist Spot

        </div>


        <div class="live-map-legend-item">

            <span
                class="
                    live-map-legend-dot
                    legend-safe-zone
                "
            ></span>

            Safe Zone

        </div>


        <div class="live-map-legend-item">

            <span
                class="
                    live-map-legend-dot
                    legend-medium-zone
                "
            ></span>

            Medium Risk

        </div>


        <div class="live-map-legend-item">

            <span
                class="
                    live-map-legend-dot
                    legend-high-zone
                "
            ></span>

            High Risk

        </div>


        <div class="live-map-legend-item">

            <span
                class="
                    live-map-legend-dot
                    legend-emergency-zone
                "
            ></span>

            Emergency

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
                        Live tourists, tourist spots, and geofence zones
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

    <script>

        const tourists =
            @json($tourists);

        const touristSpots =
            @json($touristSpots);

        const geofenceZones =
            @json($geofenceZones);


        function createPopupRow(text) {

            const span =
                document.createElement('span');

            span.textContent =
                text;

            return span;

        }


        function createTouristPopup(
            tourist,
            latitude,
            longitude
        ) {

            const container =
                document.createElement('div');

            container.className =
                'map-popup';


            const name =
                document.createElement('strong');

            name.textContent =
                tourist.full_name ??
                'Unknown Tourist';


            container.appendChild(name);

            container.appendChild(
                createPopupRow(
                    latitude.toFixed(6) +
                    ', ' +
                    longitude.toFixed(6)
                )
            );

            container.appendChild(
                createPopupRow(
                    'Account: ' +
                    (
                        tourist.is_active
                            ? 'Active'
                            : 'Inactive'
                    )
                )
            );

            container.appendChild(
                createPopupRow(
                    'Location: ' +
                    tourist.location_status
                )
            );

            container.appendChild(
                createPopupRow(
                    'Updated: ' +
                    tourist.updated_at_manila
                )
            );


            return container;

        }


        function createTouristSpotPopup(
            spot,
            latitude,
            longitude
        ) {

            const container =
                document.createElement('div');

            container.className =
                'map-popup';


            const name =
                document.createElement('strong');

            name.textContent =
                spot.name ??
                'Tourist Spot';


            container.appendChild(name);


            if (spot.category) {

                container.appendChild(
                    createPopupRow(
                        'Category: ' +
                        spot.category
                    )
                );

            }


            if (spot.address) {

                container.appendChild(
                    createPopupRow(
                        'Address: ' +
                        spot.address
                    )
                );

            }


            if (spot.opening_hours) {

                container.appendChild(
                    createPopupRow(
                        'Hours: ' +
                        spot.opening_hours
                    )
                );

            }


            container.appendChild(
                createPopupRow(
                    latitude.toFixed(6) +
                    ', ' +
                    longitude.toFixed(6)
                )
            );


            return container;

        }


        function createGeofencePopup(
            zone,
            latitude,
            longitude
        ) {

            const container =
                document.createElement('div');

            container.className =
                'map-popup';


            const name =
                document.createElement('strong');

            name.textContent =
                zone.zone_name ??
                'Geofence Zone';


            container.appendChild(name);

            container.appendChild(
                createPopupRow(
                    'Type: ' +
                    (
                        zone.zone_type ??
                        'Unknown'
                    )
                )
            );


            if (zone.tourist_spot_name) {

                container.appendChild(
                    createPopupRow(
                        'Tourist Spot: ' +
                        zone.tourist_spot_name
                    )
                );

            }


            container.appendChild(
                createPopupRow(
                    'Radius: ' +
                    Number(
                        zone.radius_meters ?? 0
                    ).toLocaleString() +
                    ' meters'
                )
            );


            if (zone.alert_message) {

                container.appendChild(
                    createPopupRow(
                        'Alert: ' +
                        zone.alert_message
                    )
                );

            }


            container.appendChild(
                createPopupRow(
                    latitude.toFixed(6) +
                    ', ' +
                    longitude.toFixed(6)
                )
            );


            return container;

        }


        function createTouristMarkerElement(
            status
        ) {

            const marker =
                document.createElement('div');

            marker.className =
                status === 'Recent'
                    ? 'tourist-map-marker tourist-map-marker-recent'
                    : 'tourist-map-marker tourist-map-marker-stale';


            const center =
                document.createElement('span');

            marker.appendChild(center);


            return marker;

        }


        function createTouristSpotMarkerElement() {

            const marker =
                document.createElement('div');

            marker.className =
                'tourist-spot-map-marker';


            const center =
                document.createElement('span');

            marker.appendChild(center);


            return marker;

        }


        function getZoneStyle(zoneType) {

            switch (zoneType) {

                case 'Safe':

                    return {
                        strokeColor: '#1B8E3E',
                        fillColor: '#1B8E3E'
                    };


                case 'Medium Risk':

                    return {
                        strokeColor: '#F59E0B',
                        fillColor: '#F59E0B'
                    };


                case 'High Risk':

                    return {
                        strokeColor: '#D32F2F',
                        fillColor: '#D32F2F'
                    };


                case 'Emergency':

                    return {
                        strokeColor: '#991B1B',
                        fillColor: '#991B1B'
                    };


                case 'Tourist':

                default:

                    return {
                        strokeColor: '#2563EB',
                        fillColor: '#2563EB'
                    };

            }

        }


        window.initTouristMap =
            async function () {

                const mapElement =
                    document.getElementById(
                        'touristMap'
                    );


                if (!mapElement) {
                    return;
                }


                const {
                    Map,
                    InfoWindow,
                    Circle
                } =
                    await google.maps.importLibrary(
                        'maps'
                    );


                const {
                    AdvancedMarkerElement
                } =
                    await google.maps.importLibrary(
                        'marker'
                    );


                const defaultCenter = {
                    lat: 10.3157,
                    lng: 123.8854
                };


                const map =
                    new Map(
                        mapElement,
                        {
                            center:
                                defaultCenter,

                            zoom:
                                13,

                            mapId:
                                'DEMO_MAP_ID',

                            mapTypeControl:
                                false,

                            streetViewControl:
                                false,

                            fullscreenControl:
                                true,

                            zoomControl:
                                true,

                            gestureHandling:
                                'greedy'
                        }
                    );


                const infoWindow =
                    new InfoWindow();


                const bounds =
                    new google.maps.LatLngBounds();


                let validMapItemCount =
                    0;


                /*
                |--------------------------------------------------------------------------
                | TOURIST LOCATION MARKERS
                |--------------------------------------------------------------------------
                */

                tourists.forEach(
                    function (tourist) {

                        if (
                            tourist.latitude === null ||
                            tourist.longitude === null
                        ) {
                            return;
                        }


                        const latitude =
                            Number(
                                tourist.latitude
                            );

                        const longitude =
                            Number(
                                tourist.longitude
                            );


                        if (
                            !Number.isFinite(
                                latitude
                            ) ||
                            !Number.isFinite(
                                longitude
                            )
                        ) {
                            return;
                        }


                        const position = {
                            lat: latitude,
                            lng: longitude
                        };


                        const marker =
                            new AdvancedMarkerElement({
                                map:
                                    map,

                                position:
                                    position,

                                title:
                                    tourist.full_name ??
                                    'Unknown Tourist',

                                content:
                                    createTouristMarkerElement(
                                        tourist.location_status
                                    ),

                                gmpClickable:
                                    true
                            });


                        marker.addEventListener(
                            'gmp-click',
                            function () {

                                infoWindow.setContent(
                                    createTouristPopup(
                                        tourist,
                                        latitude,
                                        longitude
                                    )
                                );


                                infoWindow.open({
                                    map:
                                        map,

                                    anchor:
                                        marker,

                                    shouldFocus:
                                        false
                                });

                            }
                        );


                        bounds.extend(
                            position
                        );

                        validMapItemCount++;

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | TOURIST SPOT MARKERS
                |--------------------------------------------------------------------------
                */

                touristSpots.forEach(
                    function (spot) {

                        if (
                            spot.latitude === null ||
                            spot.longitude === null
                        ) {
                            return;
                        }


                        const latitude =
                            Number(
                                spot.latitude
                            );

                        const longitude =
                            Number(
                                spot.longitude
                            );


                        if (
                            !Number.isFinite(
                                latitude
                            ) ||
                            !Number.isFinite(
                                longitude
                            )
                        ) {
                            return;
                        }


                        const position = {
                            lat: latitude,
                            lng: longitude
                        };


                        const marker =
                            new AdvancedMarkerElement({
                                map:
                                    map,

                                position:
                                    position,

                                title:
                                    spot.name ??
                                    'Tourist Spot',

                                content:
                                    createTouristSpotMarkerElement(),

                                gmpClickable:
                                    true,

                                zIndex:
                                    5
                            });


                        marker.addEventListener(
                            'gmp-click',
                            function () {

                                infoWindow.setContent(
                                    createTouristSpotPopup(
                                        spot,
                                        latitude,
                                        longitude
                                    )
                                );


                                infoWindow.open({
                                    map:
                                        map,

                                    anchor:
                                        marker,

                                    shouldFocus:
                                        false
                                });

                            }
                        );


                        bounds.extend(
                            position
                        );

                        validMapItemCount++;

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | GEOFENCE CIRCLES
                |--------------------------------------------------------------------------
                */

                geofenceZones.forEach(
                    function (zone) {

                        if (
                            zone.latitude === null ||
                            zone.longitude === null
                        ) {
                            return;
                        }


                        const latitude =
                            Number(
                                zone.latitude
                            );

                        const longitude =
                            Number(
                                zone.longitude
                            );

                        const radius =
                            Number(
                                zone.radius_meters
                            );


                        if (
                            !Number.isFinite(
                                latitude
                            ) ||
                            !Number.isFinite(
                                longitude
                            ) ||
                            !Number.isFinite(
                                radius
                            ) ||
                            radius <= 0
                        ) {
                            return;
                        }


                        const center = {
                            lat: latitude,
                            lng: longitude
                        };


                        const style =
                            getZoneStyle(
                                zone.zone_type
                            );


                        const circle =
                            new Circle({
                                map:
                                    map,

                                center:
                                    center,

                                radius:
                                    radius,

                                strokeColor:
                                    style.strokeColor,

                                strokeOpacity:
                                    0.8,

                                strokeWeight:
                                    2,

                                fillColor:
                                    style.fillColor,

                                fillOpacity:
                                    0.14,

                                clickable:
                                    true,

                                editable:
                                    false,

                                draggable:
                                    false,

                                zIndex:
                                    1
                            });


                        circle.addListener(
                            'click',
                            function (event) {

                                infoWindow.setContent(
                                    createGeofencePopup(
                                        zone,
                                        latitude,
                                        longitude
                                    )
                                );


                                infoWindow.setPosition(
                                    event.latLng ??
                                    center
                                );


                                infoWindow.open({
                                    map:
                                        map,

                                    shouldFocus:
                                        false
                                });

                            }
                        );


                        bounds.extend(
                            center
                        );

                        validMapItemCount++;

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | MAP BOUNDS
                |--------------------------------------------------------------------------
                */

                if (
                    validMapItemCount === 1
                ) {

                    map.setCenter(
                        bounds.getCenter()
                    );

                    map.setZoom(
                        15
                    );

                }

                else if (
                    validMapItemCount > 1
                ) {

                    map.fitBounds(
                        bounds,
                        50
                    );


                    google.maps.event
                        .addListenerOnce(
                            map,
                            'idle',
                            function () {

                                if (
                                    map.getZoom() !==
                                        undefined &&
                                    map.getZoom() > 15
                                ) {

                                    map.setZoom(
                                        15
                                    );

                                }

                            }
                        );

                }

                else {

                    map.setCenter(
                        defaultCenter
                    );

                    map.setZoom(
                        13
                    );

                }

            };


        window.handleTouristMapError =
            function () {

                const mapElement =
                    document.getElementById(
                        'touristMap'
                    );


                if (!mapElement) {
                    return;
                }


                mapElement.innerHTML = `
                    <div class="live-map-error">

                        <strong>
                            Google Maps could not be loaded.
                        </strong>

                        <span>
                            Check the Google Maps demo key and internet connection.
                        </span>

                    </div>
                `;

            };

    </script>


    @if (filled(env('GOOGLE_MAPS_API_KEY')))

        <script
            async
            src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&loading=async&callback=initTouristMap&v=weekly&libraries=marker"
            onerror="handleTouristMapError()"
        ></script>

    @else

        <script>

            document.addEventListener(
                'DOMContentLoaded',
                function () {

                    const mapElement =
                        document.getElementById(
                            'touristMap'
                        );


                    if (mapElement) {

                        mapElement.innerHTML = `
                            <div class="live-map-error">

                                <strong>
                                    Google Maps key is missing.
                                </strong>

                                <span>
                                    Add GOOGLE_MAPS_API_KEY to the Laravel .env file.
                                </span>

                            </div>
                        `;

                    }

                }
            );

        </script>

    @endif

@endpush