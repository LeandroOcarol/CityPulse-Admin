
@extends('admin.layouts.app')

@section('title', 'CityPulse | Tourist Density Map')
@section('body-class', 'tourist-density-map-page')

@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('css/pages/tourist-density-map.css') }}"
>

<style>
    .tourist-density-map-page .content {
        padding: 24px;
    }

    .tourist-density-map-page .live-map-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }

    .tourist-density-map-page .live-map-header h1 {
        margin: 0 0 6px;
        font-size: 26px;
        font-weight: 700;
        color: #162b20;
    }

    .density-subtitle {
        font-size: 13px;
        color: #718075;
        line-height: 1.5;
    }

    .density-privacy-notice {
        background: #eef7f0;
        color: #285b3c;
        border: 1px solid #d1e7d6;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 20px;
        font-size: 13px;
        line-height: 1.6;
    }

    .density-privacy-notice strong {
        display: block;
        margin-bottom: 3px;
        font-size: 13px;
    }

    .tourist-density-map-page .live-map-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .tourist-density-map-page .live-map-summary-card {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 10px;
        min-height: 100px;
        background: #fff;
        border: 1px solid #e1e8e3;
        border-left: 4px solid #198b46;
        border-radius: 10px;
        padding: 18px;
        box-shadow: 0 2px 7px rgba(0,0,0,.025);
    }

    .tourist-density-map-page .live-map-summary-card span {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #657569;
    }

    .tourist-density-map-page .live-map-summary-card strong {
        font-size: 28px;
        font-weight: 750;
        color: #168442;
    }

    .density-main-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 290px;
        align-items: start;
        gap: 18px;
    }

    .tourist-density-map-page .live-map-card {
        display: block;
        min-width: 0;
        border: 1px solid #e1e8e3;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,.025);
    }

    .tourist-density-map-page .live-map-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 17px 20px;
        border-bottom: 1px solid #e8eee9;
    }

    .tourist-density-map-page .live-map-card-header h2 {
        margin: 0 0 5px;
        font-size: 16px;
        color: #172b21;
    }

    .density-muted {
        font-size: 12px;
        line-height: 1.5;
        color: #768578;
    }

    .density-time-pill {
        flex-shrink: 0;
        padding: 7px 10px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 20px;
        background: #edf7f0;
        color: #17673a;
    }

    .tourist-density-map-page #touristMap {
        display: block;
        width: 100%;
        height: 510px;
        min-height: 510px;
        border: 0;
    }

    .density-map-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        padding: 13px 18px;
        border-top: 1px solid #e8eee9;
        background: #fff;
    }

    .density-legend {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px;
    }

    .density-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        color: #526657;
        white-space: nowrap;
    }

    .density-dot {
        width: 10px;
        height: 10px;
        display: inline-block;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .density-dot-low { background: #48aa6d; }
    .density-dot-medium { background: #f1b23c; }
    .density-dot-high { background: #dc5144; }
    .density-dot-spot { background: #2563eb; }
    .density-dot-zone { background: #198b46; }

    .density-side-column {
        display: flex;
        flex-direction: column;
        gap: 16px;
        min-width: 0;
    }

    .tourist-density-map-page .live-tourist-list-card,
    .density-info-card {
        border: 1px solid #e1e8e3;
        border-radius: 12px;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,.025);
    }

    .tourist-density-map-page .live-tourist-list-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 17px;
        border-bottom: 1px solid #e8eee9;
    }

    .tourist-density-map-page .live-tourist-list-header h2,
    .density-info-card h2 {
        margin: 0;
        font-size: 15px;
        color: #1b3024;
    }

    .tourist-density-map-page .live-tourist-list-header span {
        color: #778578;
        font-size: 11px;
    }

    .tourist-density-map-page .live-tourist-list {
        max-height: 365px;
        overflow-y: auto;
    }

    .density-empty-state {
        text-align: center;
        padding: 32px 20px;
    }

    .density-empty-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        margin: 0 auto 14px;
        border-radius: 50%;
        background: #eef7f0;
        color: #198b46;
        font-size: 24px;
    }

    .density-empty-state strong {
        display: block;
        font-size: 14px;
        margin-bottom: 8px;
        color: #254e34;
    }

    .density-empty-state p {
        margin: 0;
        font-size: 12px;
        line-height: 1.7;
        color: #728075;
    }

    .density-area-item {
        padding: 14px 16px;
        border-bottom: 1px solid #edf0ee;
    }

    .density-area-heading {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 10px;
        font-size: 12px;
    }

    .density-area-heading strong {
        color: #225535;
    }

    .density-bar-background {
        height: 8px;
        background: #e8f0ea;
        border-radius: 8px;
        overflow: hidden;
    }

    .density-bar-fill {
        height: 100%;
        background: #299957;
        border-radius: 8px;
    }

    .density-info-card {
        padding: 18px;
    }

    .density-info-card h2 {
        margin-bottom: 12px;
    }

    .density-info-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        padding: 9px 0;
        border-bottom: 1px solid #edf0ee;
        font-size: 12px;
    }

    .density-info-row:last-child {
        border-bottom: 0;
    }

    .density-info-row span {
        color: #708074;
    }

    .density-info-row strong {
        color: #264c34;
        text-align: right;
    }

    .density-map-popup,
    .map-popup {
        display: flex;
        flex-direction: column;
        gap: 5px;
        padding: 5px;
        font-size: 12px;
        line-height: 1.5;
    }

    .density-map-popup strong,
    .map-popup strong {
        color: #17663a;
        font-size: 13px;
    }

    @media (max-width: 1100px) {
        .density-main-layout {
            grid-template-columns: minmax(0, 1fr);
        }

        .density-side-column {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 720px) {
        .tourist-density-map-page .content {
            padding: 14px;
        }

        .tourist-density-map-page .live-map-summary {
            grid-template-columns: 1fr;
        }

        .density-side-column {
            grid-template-columns: 1fr;
        }

        .tourist-density-map-page .live-map-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .tourist-density-map-page #touristMap {
            height: 390px;
            min-height: 390px;
        }

        .density-time-pill {
            display: none;
        }
    }
</style>
@endpush


@section('content')

<div class="live-map-header">
    <div>
        <h1>Tourist Density Map</h1>

        <div class="density-subtitle">
            Geographic overview of aggregated tourist activity
            across Cebu City
        </div>
    </div>

    <a
        href="{{ route('admin.tourist-density-map.index') }}"
        class="live-map-refresh"
    >
        Refresh Density
    </a>
</div>


<div class="density-privacy-notice">
    <strong>Privacy-Conscious Tourist Monitoring</strong>

    Tourist locations are grouped into geographic areas.
    Individual names and GPS markers are not displayed.

    An area is published only when at least
    {{ $minimumGroupSize }} distinct tourists have reported
    a location within the past
    {{ $activityWindowMinutes }} minutes.
</div>


<section class="live-map-summary">

    <div class="live-map-summary-card">
        <span>Visible Density Areas</span>
        <strong>{{ number_format($visibleAreaCount) }}</strong>
    </div>

    <div class="live-map-summary-card">
        <span>Tourists in Visible Areas</span>
        <strong>{{ number_format($visibleTouristCount) }}</strong>
    </div>

    <div class="live-map-summary-card">
        <span>Highest Area Count</span>
        <strong>{{ number_format($highestAreaCount) }}</strong>
    </div>

</section>


<div class="density-main-layout">

    <section class="live-map-card">

        <div class="live-map-card-header">
            <div>
                <h2>Tourist Activity Distribution</h2>

                <span class="density-muted">
                    Aggregated density with tourist destinations
                    and geofence zones
                </span>
            </div>

            <span class="density-time-pill">
                Past {{ $activityWindowMinutes }} min
            </span>
        </div>

        <div id="touristMap" class="live-map"></div>

        <div class="density-map-footer">

            <div class="density-legend">
                <span class="density-legend-item">
                    <i class="density-dot density-dot-low"></i>
                    Lower
                </span>

                <span class="density-legend-item">
                    <i class="density-dot density-dot-medium"></i>
                    Moderate
                </span>

                <span class="density-legend-item">
                    <i class="density-dot density-dot-high"></i>
                    Higher
                </span>

                <span class="density-legend-item">
                    <i class="density-dot density-dot-spot"></i>
                    Tourist Spot
                </span>

                <span class="density-legend-item">
                    <i class="density-dot density-dot-zone"></i>
                    Geofence
                </span>
            </div>

            <span class="density-muted">
                Density colors show activity, not crime risk
            </span>

        </div>

    </section>


    <div class="density-side-column">

        <section class="live-tourist-list-card">

            <div class="live-tourist-list-header">
                <h2>Area Density Summary</h2>

                <span>
                    {{ $visibleAreaCount }} areas
                </span>
            </div>

            <div class="live-tourist-list">

                @forelse ($densityAreas as $index => $area)

                    @php
                        $percentage = $highestAreaCount > 0
                            ? $area['tourist_count']
                                / $highestAreaCount * 100
                            : 0;
                    @endphp

                    <div class="density-area-item">

                        <div class="density-area-heading">
                            <strong>Area {{ $index + 1 }}</strong>

                            <span>
                                {{ number_format($area['tourist_count']) }}
                                tourists
                            </span>
                        </div>

                        <div class="density-bar-background">
                            <div
                                class="density-bar-fill"
                                style="width: {{ $percentage }}%;"
                            ></div>
                        </div>

                    </div>

                @empty

                    <div class="density-empty-state">

                        <div class="density-empty-icon">
                            &#9678;
                        </div>

                        <strong>
                            No Density Data Available
                        </strong>

                        <p>
                            No geographic area currently meets
                            the minimum group-size and recent
                            activity requirements.

                            Density information will appear here
                            when qualifying data is available.
                        </p>

                    </div>

                @endforelse

            </div>

        </section>


        <section class="density-info-card">

            <h2>Density Information</h2>

            <div class="density-info-row">
                <span>Data Source</span>
                <strong>Tourist Location Updates</strong>
            </div>

            <div class="density-info-row">
                <span>Activity Window</span>
                <strong>{{ $activityWindowMinutes }} minutes</strong>
            </div>

            <div class="density-info-row">
                <span>Minimum Group</span>
                <strong>{{ $minimumGroupSize }} tourists</strong>
            </div>

            <div class="density-info-row">
                <span>Aggregation</span>
                <strong>Approx. 2 km Grid</strong>
            </div>

            <div class="density-info-row">
                <span>Privacy</span>
                <strong>Individual Markers Hidden</strong>
            </div>

        </section>

    </div>

</div>

@endsection


@push('scripts')
<script>

const densityAreas = @json($densityAreas);
const touristSpots = @json($touristSpots);
const geofenceZones = @json($geofenceZones);

const minimumGroupSize = @json($minimumGroupSize);

function popupRow(text) {
    const element = document.createElement('span');
    element.textContent = text;
    return element;
}

function popupContainer(title, rows) {
    const container = document.createElement('div');
    container.className = 'density-map-popup';

    const heading = document.createElement('strong');
    heading.textContent = title;
    container.appendChild(heading);

    rows.forEach(function (row) {
        if (row) {
            container.appendChild(popupRow(row));
        }
    });

    return container;
}

function zoneColors(type) {
    const colors = {
        'Safe': '#1B8E3E',
        'Medium Risk': '#F59E0B',
        'High Risk': '#D32F2F',
        'Emergency': '#991B1B'
    };

    return colors[type] || '#2563EB';
}

function densityColor(count, maximum) {
    const ratio = maximum > 0 ? count / maximum : 0;

    if (ratio >= 0.75) return '#DC5144';
    if (ratio >= 0.40) return '#F1B23C';

    return '#48AA6D';
}

function touristSpotMarker() {
    const element = document.createElement('div');
    element.className = 'tourist-spot-map-marker';

    const center = document.createElement('span');
    element.appendChild(center);

    return element;
}

window.initTouristDensityMap = async function () {
    const element = document.getElementById('touristMap');

    if (!element) return;

    try {
        const { Map, InfoWindow, Circle, Rectangle } =
            await google.maps.importLibrary('maps');

        const { AdvancedMarkerElement } =
            await google.maps.importLibrary('marker');

        const defaultCenter = {
            lat: 10.3157,
            lng: 123.8854
        };

        const map = new Map(element, {
            center: defaultCenter,
            zoom: 13,
            mapId: 'DEMO_MAP_ID',
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true,
            zoomControl: true,
            gestureHandling: 'greedy'
        });

        const infoWindow = new InfoWindow();

        const densityBounds = new google.maps.LatLngBounds();

        let densityCellCount = 0;

        const maximum = Math.max(
            0,
            ...densityAreas.map(
                area => Number(area.tourist_count) || 0
            )
        );

        /*
        |---------------------------------------------------------
        | DENSITY GRID
        |---------------------------------------------------------
        */

        densityAreas.forEach(function (area, index) {
            const latitude = Number(area.latitude);
            const longitude = Number(area.longitude);
            const count = Number(area.tourist_count);

            if (
                !Number.isFinite(latitude) ||
                !Number.isFinite(longitude) ||
                !Number.isFinite(count) ||
                count < minimumGroupSize
            ) {
                return;
            }

            const position = {
                lat: latitude,
                lng: longitude
            };

            const halfSize = 0.01;

            const areaBounds = {
                north: latitude + halfSize,
                south: latitude - halfSize,
                east: longitude + halfSize,
                west: longitude - halfSize
            };

            const color = densityColor(count, maximum);

            const rectangle = new Rectangle({
                map: map,
                bounds: areaBounds,
                strokeColor: color,
                strokeOpacity: 0.7,
                strokeWeight: 1,
                fillColor: color,
                fillOpacity: 0.35,
                clickable: true,
                zIndex: 2
            });

            rectangle.addListener('click', function (event) {
                infoWindow.setContent(
                    popupContainer(
                        'Activity Area ' + (index + 1),
                        [
                            'Recent tourists: ' +
                                count.toLocaleString(),
                            'Aggregated geographic area',
                            'No individual locations shown'
                        ]
                    )
                );

                infoWindow.setPosition(
                    event.latLng || position
                );

                infoWindow.open({
                    map: map,
                    shouldFocus: false
                });
            });

            densityBounds.extend({
                lat: areaBounds.north,
                lng: areaBounds.east
            });

            densityBounds.extend({
                lat: areaBounds.south,
                lng: areaBounds.west
            });

            densityCellCount++;
        });

        /*
        |---------------------------------------------------------
        | TOURIST SPOTS
        |---------------------------------------------------------
        */

        touristSpots.forEach(function (spot) {
            if (
                spot.latitude === null ||
                spot.longitude === null
            ) {
                return;
            }

            const latitude = Number(spot.latitude);
            const longitude = Number(spot.longitude);

            if (
                !Number.isFinite(latitude) ||
                !Number.isFinite(longitude)
            ) {
                return;
            }

            const marker = new AdvancedMarkerElement({
                map: map,
                position: {
                    lat: latitude,
                    lng: longitude
                },
                title: spot.name || 'Tourist Spot',
                content: touristSpotMarker(),
                gmpClickable: true,
                zIndex: 5
            });

            marker.addEventListener('gmp-click', function () {
                infoWindow.setContent(
                    popupContainer(
                        spot.name || 'Tourist Spot',
                        [
                            spot.category
                                ? 'Category: ' + spot.category
                                : null,
                            spot.address
                                ? 'Address: ' + spot.address
                                : null,
                            spot.opening_hours
                                ? 'Hours: ' + spot.opening_hours
                                : null
                        ]
                    )
                );

                infoWindow.open({
                    map: map,
                    anchor: marker,
                    shouldFocus: false
                });
            });
        });

        /*
        |---------------------------------------------------------
        | GEOFENCE ZONES
        |---------------------------------------------------------
        */

        geofenceZones.forEach(function (zone) {
            if (
                zone.latitude === null ||
                zone.longitude === null
            ) {
                return;
            }

            const latitude = Number(zone.latitude);
            const longitude = Number(zone.longitude);
            const radius = Number(zone.radius_meters);

            if (
                !Number.isFinite(latitude) ||
                !Number.isFinite(longitude) ||
                !Number.isFinite(radius) ||
                radius <= 0
            ) {
                return;
            }

            const center = {
                lat: latitude,
                lng: longitude
            };

            const color = zoneColors(zone.zone_type);

            const circle = new Circle({
                map: map,
                center: center,
                radius: radius,
                strokeColor: color,
                strokeOpacity: 0.75,
                strokeWeight: 2,
                fillColor: color,
                fillOpacity: 0.12,
                clickable: true,
                zIndex: 1
            });

            circle.addListener('click', function (event) {
                infoWindow.setContent(
                    popupContainer(
                        zone.zone_name || 'Geofence Zone',
                        [
                            'Type: ' +
                                (zone.zone_type || 'Unknown'),
                            'Radius: ' +
                                radius.toLocaleString() +
                                ' meters',
                            zone.tourist_spot_name
                                ? 'Tourist Spot: ' +
                                  zone.tourist_spot_name
                                : null,
                            zone.alert_message
                                ? 'Alert: ' + zone.alert_message
                                : null
                        ]
                    )
                );

                infoWindow.setPosition(
                    event.latLng || center
                );

                infoWindow.open({
                    map: map,
                    shouldFocus: false
                });
            });
        });

        /*
        |---------------------------------------------------------
        | MAP CAMERA
        |
        | Fit to density data only.
        | Tourist spots and geofences must not force the
        | camera to zoom out when density is empty.
        |---------------------------------------------------------
        */

        if (densityCellCount === 1) {
            map.setCenter(densityBounds.getCenter());
            map.setZoom(13);
        } else if (densityCellCount > 1) {
            map.fitBounds(densityBounds, 55);

            google.maps.event.addListenerOnce(
                map,
                'idle',
                function () {
                    const zoom = map.getZoom();

                    if (
                        zoom !== undefined &&
                        zoom > 14
                    ) {
                        map.setZoom(14);
                    }
                }
            );
        } else {
            map.setCenter(defaultCenter);
            map.setZoom(13);
        }

    } catch (error) {
        console.error('CityPulse Density Map:', error);
        window.handleTouristMapError();
    }
};

window.handleTouristMapError = function () {
    const element = document.getElementById('touristMap');

    if (!element) return;

    element.innerHTML = '';

    const container = document.createElement('div');
    container.className = 'live-map-error';

    const title = document.createElement('strong');
    title.textContent = 'Google Maps could not be loaded.';

    const message = document.createElement('span');
    message.textContent =
        'Check the Google Maps API key and network connection.';

    container.appendChild(title);
    container.appendChild(message);

    element.appendChild(container);
};

</script>

@if (filled(env('GOOGLE_MAPS_API_KEY')))
    <script
        async
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&loading=async&callback=initTouristDensityMap&v=weekly&libraries=marker"
        onerror="handleTouristMapError()"
    ></script>
@else
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.handleTouristMapError();
        });
    </script>
@endif

@endpush
