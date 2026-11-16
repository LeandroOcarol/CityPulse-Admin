
@extends('admin.layouts.app')

@section('title', 'CityPulse | Safety Heat Map')
@section('body-class', 'safety-heat-map-page')

@push('styles')
<style>
    .safety-heat-map-page .content {
        padding: 24px;
    }

    .safety-map-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }

    .safety-map-header h1 {
        margin: 0 0 5px;
        color: #172b21;
        font-size: 26px;
    }

    .safety-map-subtitle,
    .safety-muted {
        color: #718075;
        font-size: 12px;
        line-height: 1.6;
    }

    .safety-map-filter {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }

    .safety-map-filter select {
        min-width: 170px;
        padding: 11px 13px;
        border: 1px solid #d9e5dc;
        border-radius: 9px;
        background: #fff;
        color: #234632;
        font-size: 13px;
    }

    .safety-map-filter button,
    .safety-map-reset {
        padding: 11px 17px;
        border: 1px solid #168442;
        border-radius: 9px;
        background: #168442;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    .safety-map-reset {
        background: #fff;
        color: #17663a;
    }

    .safety-map-notice {
        margin-bottom: 20px;
        padding: 14px 17px;
        border-radius: 10px;
        border: 1px solid #d2e6d6;
        background: #f0f7f1;
        color: #315b3e;
        font-size: 13px;
        line-height: 1.6;
    }

    .safety-map-notice strong {
        display: block;
        margin-bottom: 4px;
    }

    .safety-map-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .safety-map-summary-card {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 10px;
        min-height: 108px;
        padding: 18px;
        border: 1px solid #e1e8e3;
        border-left: 4px solid #198b46;
        border-radius: 10px;
        background: #fff;
    }

    .safety-map-summary-card span {
        color: #64776a;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
    }

    .safety-map-summary-card strong {
        color: #168442;
        font-size: 28px;
    }

    .safety-map-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 295px;
        align-items: start;
        gap: 18px;
    }

    .safety-map-panel,
    .safety-side-card {
        min-width: 0;
        overflow: hidden;
        border: 1px solid #e1e8e3;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,.025);
    }

    .safety-map-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        padding: 18px;
        border-bottom: 1px solid #edf0ee;
    }

    .safety-map-panel-header h2,
    .safety-side-card h2 {
        margin: 0 0 5px;
        font-size: 16px;
        color: #1b3024;
    }

    .safety-period-tag {
        padding: 7px 11px;
        border-radius: 20px;
        background: #eef7f1;
        color: #226b3c;
        font-size: 11px;
        font-weight: 700;
    }

    #safetyHeatMap {
        width: 100%;
        height: 520px;
        background: #e9f0ed;
    }

    .safety-map-footer {
        padding: 15px 18px;
        border-top: 1px solid #edf0ee;
    }

    .safety-map-legend {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 9px;
    }

    .safety-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #506457;
        font-size: 12px;
    }

    .safety-legend-dot {
        display: inline-block;
        width: 11px;
        height: 11px;
        border-radius: 50%;
    }

    .safety-legend-low {
        background: #4caf70;
    }

    .safety-legend-moderate {
        background: #f2b744;
    }

    .safety-legend-high {
        background: #dc5144;
    }

    .safety-side-column {
        display: flex;
        flex-direction: column;
        gap: 16px;
        min-width: 0;
    }

    .safety-side-card h2 {
        padding: 17px;
        margin: 0;
        border-bottom: 1px solid #edf0ee;
    }

    .safety-hotspot-list {
        max-height: 340px;
        overflow-y: auto;
    }

    .safety-hotspot-item {
        padding: 14px 16px;
        border-bottom: 1px solid #edf0ee;
    }

    .safety-hotspot-heading {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 9px;
        color: #526457;
        font-size: 12px;
    }

    .safety-hotspot-heading strong {
        color: #245538;
    }

    .safety-hotspot-bar {
        height: 8px;
        overflow: hidden;
        border-radius: 6px;
        background: #e8efe9;
    }

    .safety-hotspot-progress {
        height: 100%;
        border-radius: 6px;
    }

    .safety-hotspot-description {
        margin-top: 7px;
        color: #78867c;
        font-size: 11px;
    }

    .safety-side-information {
        padding: 5px 17px;
    }

    .safety-info-row {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        padding: 11px 0;
        border-bottom: 1px solid #edf0ee;
        font-size: 12px;
    }

    .safety-info-row:last-child {
        border-bottom: none;
    }

    .safety-info-row span {
        color: #708075;
    }

    .safety-info-row strong {
        color: #285438;
        text-align: right;
    }

    .safety-map-empty {
        padding: 30px 17px;
        color: #748278;
        font-size: 12px;
        line-height: 1.7;
        text-align: center;
    }

    .safety-map-popup {
        display: flex;
        flex-direction: column;
        gap: 5px;
        padding: 6px;
        font-size: 12px;
        line-height: 1.5;
    }

    .safety-map-popup strong {
        color: #17663a;
        font-size: 14px;
    }

    .safety-map-error {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 8px;
        height: 100%;
        padding: 25px;
        color: #566b5b;
        text-align: center;
    }

    @media (max-width: 1100px) {
        .safety-map-layout {
            grid-template-columns: minmax(0, 1fr);
        }

        .safety-side-column {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 720px) {
        .safety-heat-map-page .content {
            padding: 14px;
        }

        .safety-map-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .safety-side-column {
            grid-template-columns: 1fr;
        }

        #safetyHeatMap {
            height: 400px;
        }
    }
</style>
@endpush

@section('content')

<div class="safety-map-header">
    <div>
        <h1>Safety Heat Map</h1>

        <div class="safety-map-subtitle">
            Historical crime concentration analysis
            in the Cebu City study area
        </div>
    </div>

    <form
        method="GET"
        action="{{ route('admin.safety-heat-map.index') }}"
        class="safety-map-filter"
    >
        <select name="period">
            <option
                value="all"
                {{ $period === 'all' ? 'selected' : '' }}
            >
                All Available Records
            </option>

            <option
                value="30days"
                {{ $period === '30days' ? 'selected' : '' }}
            >
                Last 30 Days
            </option>

            <option
                value="90days"
                {{ $period === '90days' ? 'selected' : '' }}
            >
                Last 90 Days
            </option>

            <option
                value="year"
                {{ $period === 'year' ? 'selected' : '' }}
            >
                Current Year
            </option>
        </select>

        <button type="submit">Apply Filter</button>

        <button
            type="button"
            class="safety-map-reset"
            onclick="window.resetSafetyMapView()"
        >
            Reset Map
        </button>
    </form>
</div>

<div class="safety-map-notice">
    <strong>Historical Crime Concentration</strong>

    This module shows recorded crime concentrations
    within a provisional Cebu-area geographic window.

    It does not yet use a verified Cebu City administrative
    boundary. Neighboring areas may be included and some
    parts of the city may be excluded.

    Historical concentrations are not predictions of
    future crime or guarantees of safety.
</div>

<section class="safety-map-summary">
    <div class="safety-map-summary-card">
        <span>Mapped Crime Records</span>
        <strong>{{ number_format($totalRecords) }}</strong>
    </div>

    <div class="safety-map-summary-card">
        <span>Recorded Incidents</span>
        <strong>{{ number_format($totalIncidents) }}</strong>
    </div>

    <div class="safety-map-summary-card">
        <span>Crime Concentration Areas</span>
        <strong>{{ number_format($hotspotCount) }}</strong>
    </div>

    <div class="safety-map-summary-card">
        <span>High-Severity Incidents</span>
        <strong>{{ number_format($highSeverityCount) }}</strong>
    </div>
</section>

<div class="safety-map-layout">

    <section class="safety-map-panel">

        <div class="safety-map-panel-header">
            <div>
                <h2>Historical Crime Heat Map</h2>

                <div class="safety-muted">
                    Grouped geographic concentrations of
                    recorded crime incidents
                </div>
            </div>

            <span class="safety-period-tag">
                {{ $periodLabel }}
            </span>
        </div>

        <div id="safetyHeatMap"></div>

        <div class="safety-map-footer">
            <div class="safety-map-legend">
                <span class="safety-legend-item">
                    <i class="safety-legend-dot safety-legend-low"></i>
                    Lower Concentration
                </span>

                <span class="safety-legend-item">
                    <i class="safety-legend-dot safety-legend-moderate"></i>
                    Moderate
                </span>

                <span class="safety-legend-item">
                    <i class="safety-legend-dot safety-legend-high"></i>
                    Higher Concentration
                </span>
            </div>

            <div class="safety-muted">
                Colors show relative historical incident
                counts, not predictive risk classifications.
            </div>
        </div>

    </section>

    <div class="safety-side-column">

        <section class="safety-side-card">
            <h2>Crime Concentration Summary</h2>

            <div class="safety-hotspot-list">
                @forelse ($hotspots as $index => $hotspot)
                    @php
                        $count = $hotspot['incident_count'];

                        $ratio = $maximumIncidentCount > 0
                            ? $count / $maximumIncidentCount
                            : 0;

                        $color = $ratio >= 0.75
                            ? '#DC5144'
                            : (
                                $ratio >= 0.40
                                ? '#F2B744'
                                : '#4CAF70'
                            );
                    @endphp

                    <div class="safety-hotspot-item">
                        <div class="safety-hotspot-heading">
                            <strong>Area {{ $index + 1 }}</strong>

                            <span>
                                {{ number_format($count) }}
                                incidents
                            </span>
                        </div>

                        <div class="safety-hotspot-bar">
                            <div
                                class="safety-hotspot-progress"
                                style="
                                    width: {{ $ratio * 100 }}%;
                                    background: {{ $color }};
                                "
                            ></div>
                        </div>

                        <div class="safety-hotspot-description">
                            {{ number_format($hotspot['record_count']) }}
                            source records
                        </div>
                    </div>
                @empty
                    <div class="safety-map-empty">
                        No mapped crime records were found
                        in this geographic window and period.
                    </div>
                @endforelse
            </div>
        </section>

        <section class="safety-side-card">
            <h2>Map Information</h2>

            <div class="safety-side-information">
                <div class="safety-info-row">
                    <span>Data Source</span>
                    <strong>Supabase Crime Records</strong>
                </div>

                <div class="safety-info-row">
                    <span>Selected Period</span>
                    <strong>{{ $periodLabel }}</strong>
                </div>

                <div class="safety-info-row">
                    <span>Area Filter</span>
                    <strong>{{ $coverageLabel }}</strong>
                </div>

                <div class="safety-info-row">
                    <span>Grid Size</span>
                    <strong>Approx. 1 km</strong>
                </div>

                <div class="safety-info-row">
                    <span>Mapped Records Excluded</span>
                    <strong>
                        {{ number_format($excludedRecordCount) }}
                    </strong>
                </div>

                <div class="safety-info-row">
                    <span>Analysis</span>
                    <strong>Historical, Not Predictive</strong>
                </div>
            </div>
        </section>

    </div>
</div>

@endsection

@push('scripts')
<script>
    const crimeHotspots = @json($hotspots);

    const highestIncidentCount =
        @json($maximumIncidentCount);

    const coverageBounds = @json($coverageBounds);

    const CEBU_CENTER = {
        lat: 10.3157,
        lng: 123.8854
    };

    const DEFAULT_ZOOM = 12;

    let safetyMapInstance = null;

    function makeSafetyPopup(title, lines) {
        const container = document.createElement('div');
        container.className = 'safety-map-popup';

        const heading = document.createElement('strong');
        heading.textContent = title;
        container.appendChild(heading);

        lines.forEach(function (line) {
            const span = document.createElement('span');
            span.textContent = line;
            container.appendChild(span);
        });

        return container;
    }

    function getSafetyColor(count) {
        const ratio = highestIncidentCount > 0
            ? count / highestIncidentCount
            : 0;

        if (ratio >= 0.75) return '#DC5144';
        if (ratio >= 0.40) return '#F2B744';

        return '#4CAF70';
    }

    window.resetSafetyMapView = function () {
        if (!safetyMapInstance) return;

        safetyMapInstance.setCenter(CEBU_CENTER);
        safetyMapInstance.setZoom(DEFAULT_ZOOM);
    };

    window.initSafetyHeatMap = async function () {
        const element = document.getElementById(
            'safetyHeatMap'
        );

        if (!element) return;

        try {
            const { Map, Circle, InfoWindow } =
                await google.maps.importLibrary('maps');

            const map = new Map(element, {
                center: CEBU_CENTER,
                zoom: DEFAULT_ZOOM,
                mapId: 'DEMO_MAP_ID',
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
                zoomControl: true,
                gestureHandling: 'greedy'
            });

            safetyMapInstance = map;

            const infoWindow = new InfoWindow();

            /*
            |--------------------------------------------------------------------------
            | HISTORICAL CRIME OVERLAYS
            |--------------------------------------------------------------------------
            |
            | Overlay center points are aggregated
            | geographic cells, not exact incident points.
            |
            */

            crimeHotspots.forEach(function (hotspot, index) {
                if (
                    hotspot.latitude === null ||
                    hotspot.longitude === null
                ) {
                    return;
                }

                const latitude = Number(hotspot.latitude);
                const longitude = Number(hotspot.longitude);
                const count = Number(hotspot.incident_count);

                if (
                    !Number.isFinite(latitude) ||
                    !Number.isFinite(longitude) ||
                    !Number.isFinite(count) ||
                    count <= 0
                ) {
                    return;
                }

                const center = {
                    lat: latitude,
                    lng: longitude
                };

                const ratio = highestIncidentCount > 0
                    ? count / highestIncidentCount
                    : 0;

                const color = getSafetyColor(count);

                [
                    {
                        radius: 1050,
                        opacity: 0.035 + ratio * 0.055
                    },
                    {
                        radius: 730,
                        opacity: 0.055 + ratio * 0.085
                    },
                    {
                        radius: 420,
                        opacity: 0.08 + ratio * 0.14
                    }
                ].forEach(function (layer) {
                    new Circle({
                        map: map,
                        center: center,
                        radius: layer.radius,
                        strokeOpacity: 0,
                        fillColor: color,
                        fillOpacity: layer.opacity,
                        clickable: false,
                        zIndex: 1
                    });
                });

                const clickableArea = new Circle({
                    map: map,
                    center: center,
                    radius: 420,
                    strokeColor: color,
                    strokeOpacity: 0.35,
                    strokeWeight: 1,
                    fillColor: color,
                    fillOpacity: 0.025,
                    clickable: true,
                    zIndex: 2
                });

                clickableArea.addListener(
                    'click',
                    function (event) {
                        infoWindow.setContent(
                            makeSafetyPopup(
                                'Crime Concentration Area ' +
                                    (index + 1),
                                [
                                    'Recorded incidents: ' +
                                        count.toLocaleString(),

                                    'Source records: ' +
                                        Number(
                                            hotspot.record_count
                                        ).toLocaleString(),

                                    'High-severity incidents: ' +
                                        Number(
                                            hotspot.high_severity_count
                                        ).toLocaleString(),

                                    'Historical data only'
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
                    }
                );
            });

            /*
            |--------------------------------------------------------------------------
            | INITIAL CAMERA
            |--------------------------------------------------------------------------
            |
            | Always start in Cebu City.
            | Do not fit the map to every crime hotspot.
            |
            */

            map.setCenter(CEBU_CENTER);
            map.setZoom(DEFAULT_ZOOM);

        } catch (error) {
            console.error(
                'CityPulse Safety Heat Map:',
                error
            );

            window.safetyHeatMapError();
        }
    };

    window.safetyHeatMapError = function () {
        const element = document.getElementById(
            'safetyHeatMap'
        );

        if (!element) return;

        element.innerHTML = '';

        const container = document.createElement('div');
        container.className = 'safety-map-error';

        const heading = document.createElement('strong');
        heading.textContent = 'Google Maps could not be loaded.';

        const message = document.createElement('span');
        message.textContent =
            'Check your Google Maps API key and connection.';

        container.appendChild(heading);
        container.appendChild(message);
        element.appendChild(container);
    };
</script>

@if (filled(env('GOOGLE_MAPS_API_KEY')))
    <script
        async
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&loading=async&callback=initSafetyHeatMap&v=weekly"
        onerror="safetyHeatMapError()"
    ></script>
@else
    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function () {
                window.safetyHeatMapError();
            }
        );
    </script>
@endif
@endpush