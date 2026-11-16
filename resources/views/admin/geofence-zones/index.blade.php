
@extends('admin.layouts.app')

@section('title', 'CityPulse | Geofence Zones')
@section('body-class', 'geofence-zones-page')

@push('styles')
<style>
    .geofence-zones-page .content {
        padding: 24px;
    }

    .geofence-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .geofence-header h1 {
        margin: 0 0 6px;
        color: #192f22;
        font-size: 26px;
    }

    .geofence-subtitle {
        color: #708075;
        font-size: 13px;
        line-height: 1.6;
    }

    .geofence-refresh {
        padding: 11px 16px;
        border: 1px solid #198b46;
        border-radius: 9px;
        background: #fff;
        color: #168442;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }

    .geofence-refresh:hover {
        background: #edf8f0;
    }

    .geofence-notice {
        padding: 15px 18px;
        margin-bottom: 20px;
        border: 1px solid #d1e6d6;
        border-radius: 10px;
        background: #eef7f0;
        color: #285c3b;
        font-size: 13px;
        line-height: 1.6;
    }

    .geofence-notice strong {
        display: block;
        margin-bottom: 4px;
    }

    .geofence-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .geofence-summary-card {
        padding: 18px;
        min-height: 98px;
        border: 1px solid #e2e9e3;
        border-left: 4px solid #198b46;
        border-radius: 11px;
        background: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 9px;
    }

    .geofence-summary-card span {
        font-size: 11px;
        color: #687a6d;
        letter-spacing: .4px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .geofence-summary-card strong {
        font-size: 28px;
        color: #178542;
    }

    .geofence-filters {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 18px;
        padding: 15px;
        background: #fff;
        border: 1px solid #e2e9e3;
        border-radius: 11px;
    }

    .geofence-filters input,
    .geofence-filters select {
        padding: 11px 12px;
        border: 1px solid #dbe6dd;
        border-radius: 8px;
        color: #253e2c;
        background: #fff;
        font-size: 13px;
        min-width: 170px;
        max-width: 100%;
    }

    .geofence-filters input {
        flex: 1;
        min-width: 200px;
    }

    .geofence-filter-button {
        padding: 11px 18px;
        background: #198b46;
        border: 1px solid #198b46;
        border-radius: 8px;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    .geofence-filter-button:hover {
        background: #13733a;
    }

    .geofence-filter-reset {
        color: #17663a;
        font-size: 13px;
        text-decoration: none;
        padding: 10px;
    }

    .geofence-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 335px;
        gap: 18px;
        align-items: start;
    }

    .geofence-map-card,
    .geofence-list-card,
    .geofence-info-card {
        background: #fff;
        border: 1px solid #e2e9e3;
        border-radius: 12px;
        overflow: hidden;
        min-width: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,.025);
    }

    .geofence-card-header {
        padding: 17px 18px;
        border-bottom: 1px solid #edf0ee;
    }

    .geofence-card-header h2 {
        font-size: 16px;
        margin: 0 0 5px;
        color: #203528;
    }

    .geofence-card-header span {
        color: #758378;
        font-size: 12px;
        line-height: 1.5;
    }

    #geofenceMap {
        height: 530px;
        width: 100%;
        background: #e9f0ed;
    }

    .geofence-map-legend {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        align-items: center;
        padding: 15px 18px;
        border-top: 1px solid #edf0ee;
    }

    .geofence-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        color: #586b5d;
    }

    .geofence-legend-dot {
        width: 11px;
        height: 11px;
        display: inline-block;
        border-radius: 50%;
    }

    .geofence-legend-safe {
        background: #1b8e3e;
    }

    .geofence-legend-medium {
        background: #f59e0b;
    }

    .geofence-legend-high {
        background: #d32f2f;
    }

    .geofence-legend-tourist {
        background: #2563eb;
    }

    .geofence-side-column {
        display: flex;
        flex-direction: column;
        gap: 16px;
        min-width: 0;
    }

    .geofence-list {
        max-height: 480px;
        overflow-y: auto;
    }

    .geofence-zone-item {
        width: 100%;
        padding: 15px 17px;
        text-align: left;
        display: block;
        border: 0;
        border-bottom: 1px solid #ecf0ed;
        background: #fff;
        cursor: pointer;
        font-family: inherit;
    }

    .geofence-zone-item:hover,
    .geofence-zone-item:focus {
        background: #f0f8f2;
        outline-offset: -2px;
    }

    .geofence-zone-item-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 9px;
    }

    .geofence-zone-item-heading strong {
        color: #253d2b;
        font-size: 13px;
    }

    .geofence-zone-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
        background: #eef2ef;
        color: #3c5a45;
    }

    .geofence-zone-meta {
        color: #718075;
        font-size: 12px;
        line-height: 1.6;
    }

    .geofence-info-card {
        padding: 17px;
        font-size: 12px;
        color: #627266;
        line-height: 1.7;
    }

    .geofence-info-card strong {
        color: #285338;
        display: block;
        font-size: 14px;
        margin-bottom: 7px;
    }

    .geofence-info-card p {
        margin: 0 0 10px;
    }

    .geofence-empty {
        padding: 30px 18px;
        text-align: center;
        font-size: 13px;
        color: #738175;
        line-height: 1.7;
    }

    .geofence-map-error {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        height: 100%;
        color: #65776a;
        text-align: center;
        padding: 20px;
    }

    .geofence-popup {
        display: flex;
        flex-direction: column;
        gap: 5px;
        padding: 5px;
        min-width: 170px;
        font-size: 12px;
        line-height: 1.5;
    }

    .geofence-popup strong {
        color: #17663a;
        font-size: 14px;
    }

    @media (max-width: 1100px) {
        .geofence-layout {
            grid-template-columns: 1fr;
        }

        .geofence-side-column {
            display: grid;
            grid-template-columns: repeat(
                2,
                minmax(0, 1fr)
            );
        }
    }

    @media (max-width: 700px) {
        .geofence-zones-page .content {
            padding: 14px;
        }

        .geofence-summary {
            grid-template-columns: 1fr;
        }

        .geofence-side-column {
            grid-template-columns: 1fr;
        }

        #geofenceMap {
            height: 400px;
        }

        .geofence-filters input,
        .geofence-filters select {
            flex: 1 1 100%;
        }
    }
</style>
@endpush


@section('content')

<!-- PAGE HEADER -->

<div class="geofence-header">

    <div>
        <h1>Geofence Zones</h1>

        <div class="geofence-subtitle">
            Geographic boundaries for tourist destinations
            and safety classifications across CityPulse.
        </div>
    </div>

    <a
        class="geofence-refresh"
        href="{{ route('admin.geofence-zones.index') }}"
    >
        Refresh Zones
    </a>

</div>


<!-- INFORMATION NOTICE -->

<div class="geofence-notice">

    <strong>
        Geofence Zone Monitoring
    </strong>

    This dashboard displays existing destination
    and safety-related geographic zones.

    Zone information is retrieved from the shared
    CityPulse database without changing its records.

    Tourist destination geofences are intended to
    follow the Tourist Spots management workflow.

</div>


<!-- SUMMARY CARDS -->

<section class="geofence-summary">

    <div class="geofence-summary-card">

        <span>
            Total Supported Zones
        </span>

        <strong>
            {{ number_format($totalZones) }}
        </strong>

    </div>


    <div class="geofence-summary-card">

        <span>
            Medium & High Risk Zones
        </span>

        <strong>
            {{ number_format(
                $counts['Medium Risk'] + $counts['High Risk']
            ) }}
        </strong>

    </div>


    <div class="geofence-summary-card">

        <span>
            Mapped Filter Results
        </span>

        <strong>
            {{ number_format($mappedZoneCount) }}
        </strong>

    </div>

</section>


<!-- ZONE FILTERS -->

<form
    method="GET"
    action="{{ route('admin.geofence-zones.index') }}"
    class="geofence-filters"
>

    <input
        type="search"
        name="search"
        placeholder="Search zone or tourist spot"
        value="{{ $search }}"
        maxlength="100"
        aria-label="Search geofence zones"
    >

    <select
        name="type"
        aria-label="Filter by zone type"
    >

        <option
            value="all"
            {{ $selectedType === 'all' ? 'selected' : '' }}
        >
            All Zone Types
        </option>

        @foreach ($zoneTypes as $zoneType)

            <option
                value="{{ $zoneType }}"
                {{ $selectedType === $zoneType
                    ? 'selected'
                    : '' }}
            >
                {{ $zoneType }}
            </option>

        @endforeach

    </select>

    <button
        type="submit"
        class="geofence-filter-button"
    >
        Apply Filter
    </button>

    <a
        href="{{ route('admin.geofence-zones.index') }}"
        class="geofence-filter-reset"
    >
        Clear
    </a>

</form>


<!-- GOOGLE MAP AND ZONE DIRECTORY -->

<div class="geofence-layout">

    <section class="geofence-map-card">

        <div class="geofence-card-header">

            <h2>
                Geographic Zone Boundaries
            </h2>

            <span>
                Click a circle to view zone information.
                You can also select a zone from the directory.
            </span>

        </div>


        <div id="geofenceMap"></div>


        <!-- ZONE LEGEND -->

        <div class="geofence-map-legend">

            <span class="geofence-legend-item">
                <i
                    class="geofence-legend-dot geofence-legend-safe"
                ></i>
                Safe
            </span>

            <span class="geofence-legend-item">
                <i
                    class="geofence-legend-dot geofence-legend-medium"
                ></i>
                Medium Risk
            </span>

            <span class="geofence-legend-item">
                <i
                    class="geofence-legend-dot geofence-legend-high"
                ></i>
                High Risk
            </span>

            <span class="geofence-legend-item">
                <i
                    class="geofence-legend-dot geofence-legend-tourist"
                ></i>
                Tourist
            </span>

        </div>

    </section>


    <!-- ZONE DIRECTORY -->

    <div class="geofence-side-column">

        <section class="geofence-list-card">

            <div class="geofence-card-header">

                <h2>
                    Zone Directory
                </h2>

                <span>
                    {{ number_format($filteredZoneCount) }}
                    matching zones
                </span>

            </div>


            <div class="geofence-list">

                @forelse ($zones as $index => $zone)

                    <button
                        type="button"
                        class="geofence-zone-item"
                        data-zone-index="{{ $index }}"
                    >

                        <div class="geofence-zone-item-heading">

                            <strong>
                                {{ $zone['zone_name']
                                    ?: 'Unnamed Zone' }}
                            </strong>

                            <span class="geofence-zone-badge">
                                {{ $zone['zone_type'] }}
                            </span>

                        </div>


                        <div class="geofence-zone-meta">

                            Radius:
                            {{ number_format(
                                $zone['radius_meters']
                            ) }}
                            meters

                            @if ($zone['tourist_spot_name'])

                                <div>
                                    Tourist Spot:
                                    {{ $zone['tourist_spot_name'] }}
                                </div>

                            @endif

                            @if ($zone['alert_message'])

                                <div>
                                    Alert:
                                    {{ $zone['alert_message'] }}
                                </div>

                            @endif

                        </div>

                    </button>

                @empty

                    <div class="geofence-empty">

                        No geofence zones match the
                        selected filters.

                    </div>

                @endforelse

            </div>

        </section>


        <!-- ADDITIONAL INFORMATION -->

        <section class="geofence-info-card">

            <strong>
                Geofence Information
            </strong>

            <p>
                Green, amber, and red indicate configured
                safety classifications. Blue identifies
                tourist destination geofences.
            </p>

            <p>
                A tourist destination is not automatically
                classified as safe. Destination zones
                and safety-risk zones serve different purposes.
            </p>

            <p>
                This page is read-only. Tourist spot creation
                and updates should manage their associated
                destination geofences once that integration
                has been verified.
            </p>

        </section>

    </div>

</div>

@endsection


@push('scripts')

<script>

    /*
    |--------------------------------------------------------------------------
    | EXISTING GEOFENCE DATA
    |--------------------------------------------------------------------------
    */

    const geofenceZones = @json($zones);


    /*
    |--------------------------------------------------------------------------
    | SUPPORTED ZONE COLORS
    |--------------------------------------------------------------------------
    */

    const zoneColors = {
        'Safe': '#1B8E3E',
        'Medium Risk': '#F59E0B',
        'High Risk': '#D32F2F',
        'Tourist': '#2563EB'
    };


    const defaultCenter = {
        lat: 10.3157,
        lng: 123.8854
    };


    let geofenceMapInstance = null;

    let geofenceInfoWindow = null;

    const zoneCircleLookup = new Map();


    /*
    |--------------------------------------------------------------------------
    | ZONE POPUP
    |--------------------------------------------------------------------------
    */

    function makeZonePopup(zone) {

        const container = document.createElement('div');

        container.className = 'geofence-popup';

        const title = document.createElement('strong');

        title.textContent =
            zone.zone_name || 'Unnamed Zone';

        container.appendChild(title);

        const lines = [
            'Zone Type: ' +
                (zone.zone_type || 'Unknown'),

            'Radius: ' +
                Number(
                    zone.radius_meters
                ).toLocaleString() +
                ' meters',

            zone.tourist_spot_name
                ? 'Tourist Spot: ' +
                    zone.tourist_spot_name
                : null,

            zone.alert_message
                ? 'Alert: ' +
                    zone.alert_message
                : null
        ];

        lines.forEach(function (line) {

            if (!line) {
                return;
            }

            const row = document.createElement('span');

            row.textContent = line;

            container.appendChild(row);

        });

        return container;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATE DISPLAY COORDINATES
    |--------------------------------------------------------------------------
    */

    function isValidZone(zone) {

        if (
            zone.latitude === null ||
            zone.longitude === null
        ) {
            return false;
        }

        const latitude = Number(zone.latitude);

        const longitude = Number(zone.longitude);

        const radius = Number(zone.radius_meters);

        return (
            Number.isFinite(latitude) &&
            Number.isFinite(longitude) &&
            Number.isFinite(radius) &&
            latitude >= -90 &&
            latitude <= 90 &&
            longitude >= -180 &&
            longitude <= 180 &&
            radius > 0
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FOCUS ZONE FROM DIRECTORY
    |--------------------------------------------------------------------------
    */

    function focusGeofenceZone(index) {

        const entry = zoneCircleLookup.get(index);

        if (
            !entry ||
            !geofenceMapInstance ||
            !geofenceInfoWindow
        ) {
            return;
        }

        const zone = geofenceZones[index];

        const center = entry.center;

        const bounds = entry.circle.getBounds();

        if (bounds) {

            geofenceMapInstance.fitBounds(
                bounds,
                65
            );

            google.maps.event.addListenerOnce(
                geofenceMapInstance,
                'idle',
                function () {

                    const zoom =
                        geofenceMapInstance.getZoom();

                    if (
                        zoom !== undefined &&
                        zoom > 16
                    ) {
                        geofenceMapInstance.setZoom(16);
                    }

                }
            );

        } else {

            geofenceMapInstance.setCenter(center);

            geofenceMapInstance.setZoom(15);
        }

        geofenceInfoWindow.setContent(
            makeZonePopup(zone)
        );

        geofenceInfoWindow.setPosition(center);

        geofenceInfoWindow.open({
            map: geofenceMapInstance,
            shouldFocus: false
        });
    }


    /*
    |--------------------------------------------------------------------------
    | INITIALIZE GOOGLE MAP
    |--------------------------------------------------------------------------
    */

    window.initGeofenceMap = async function () {

        const element =
            document.getElementById('geofenceMap');

        if (!element) {
            return;
        }

        try {

            const {
                Map,
                Circle,
                InfoWindow
            } = await google.maps.importLibrary('maps');


            const map = new Map(
                element,
                {
                    center: defaultCenter,
                    zoom: 12,
                    mapId: 'DEMO_MAP_ID',
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: true,
                    zoomControl: true,
                    cameraControl: false,
                    gestureHandling: 'greedy'
                }
            );


            geofenceMapInstance = map;

            geofenceInfoWindow =
                new InfoWindow();


            /*
            |--------------------------------------------------------------------------
            | DISPLAY GEOFENCE CIRCLES
            |--------------------------------------------------------------------------
            */

            geofenceZones.forEach(
                function (zone, index) {

                    if (!isValidZone(zone)) {
                        return;
                    }

                    const center = {
                        lat: Number(zone.latitude),
                        lng: Number(zone.longitude)
                    };

                    const color =
                        zoneColors[zone.zone_type] ||
                        '#64748B';

                    const circle = new Circle({
                        map: map,

                        center: center,

                        radius: Number(
                            zone.radius_meters
                        ),

                        strokeColor: color,
                        strokeOpacity: 0.85,
                        strokeWeight: 2,

                        fillColor: color,
                        fillOpacity: 0.17,

                        clickable: true,
                        editable: false,
                        draggable: false,

                        zIndex: 2
                    });


                    circle.addListener(
                        'click',
                        function (event) {

                            geofenceInfoWindow.setContent(
                                makeZonePopup(zone)
                            );

                            geofenceInfoWindow.setPosition(
                                event.latLng || center
                            );

                            geofenceInfoWindow.open({
                                map: map,
                                shouldFocus: false
                            });
                        }
                    );


                    zoneCircleLookup.set(
                        index,
                        {
                            center: center,
                            circle: circle
                        }
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | DIRECTORY CLICK HANDLERS
            |--------------------------------------------------------------------------
            */

            document.querySelectorAll(
                '[data-zone-index]'
            ).forEach(function (button) {

                button.addEventListener(
                    'click',
                    function () {

                        const index = Number(
                            button.dataset.zoneIndex
                        );

                        focusGeofenceZone(index);

                    }
                );

            });

        } catch (error) {

            console.error(
                'CityPulse Geofence Zones:',
                error
            );

            window.showGeofenceMapError();

        }
    };


    /*
    |--------------------------------------------------------------------------
    | MAP ERROR HANDLING
    |--------------------------------------------------------------------------
    */

    window.showGeofenceMapError = function () {

        const element =
            document.getElementById('geofenceMap');

        if (!element) {
            return;
        }

        element.innerHTML = '';

        const box = document.createElement('div');

        box.className = 'geofence-map-error';

        const title = document.createElement('strong');

        title.textContent =
            'Google Maps could not be loaded.';

        const message = document.createElement('span');

        message.textContent =
            'Check the Google Maps API key and internet connection.';

        box.appendChild(title);

        box.appendChild(message);

        element.appendChild(box);
    };

</script>


@if (filled(env('GOOGLE_MAPS_API_KEY')))

    <script
        async
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&loading=async&callback=initGeofenceMap&v=weekly"
        onerror="showGeofenceMapError()"
    ></script>

@else

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                window.showGeofenceMapError();

            }
        );

    </script>

@endif

@endpush