
@extends('admin.layouts.app')

@section('title', $mode === 'edit'
    ? 'CityPulse | Edit Geofence Zone'
    : 'CityPulse | Add Geofence Zone'
)

@section('body-class', 'geofence-editor-page')

@push('styles')
<style>
    .geofence-editor-page .content {
        padding: 24px;
    }

    .zone-editor-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 22px;
    }

    .zone-editor-header h1 {
        margin: 0 0 6px;
        color: #192f22;
        font-size: 26px;
    }

    .zone-editor-subtitle {
        color: #708075;
        font-size: 13px;
        line-height: 1.6;
    }

    .zone-editor-back {
        color: #168442;
        background: #fff;
        text-decoration: none;
        border: 1px solid #198b46;
        border-radius: 9px;
        padding: 10px 15px;
        font-size: 13px;
        font-weight: 600;
    }

    .zone-editor-back:hover {
        background: #edf8f0;
    }

    .zone-editor-layout {
        display: grid;
        grid-template-columns: minmax(0, 430px) minmax(0, 1fr);
        align-items: start;
        gap: 18px;
    }

    .zone-editor-card {
        background: #fff;
        border: 1px solid #e2e9e3;
        border-radius: 12px;
        overflow: hidden;
        min-width: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,.025);
    }

    .zone-editor-card-header {
        padding: 18px;
        border-bottom: 1px solid #edf0ee;
    }

    .zone-editor-card-header h2 {
        margin: 0 0 5px;
        font-size: 16px;
        color: #203528;
    }

    .zone-editor-card-header p {
        margin: 0;
        color: #758378;
        font-size: 12px;
        line-height: 1.6;
    }

    .zone-editor-fields {
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 17px;
    }

    .zone-editor-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .zone-editor-field label {
        color: #253d2b;
        font-weight: 700;
        font-size: 12px;
    }

    .zone-editor-field input,
    .zone-editor-field select,
    .zone-editor-field textarea {
        box-sizing: border-box;
        width: 100%;
        min-width: 0;
        border: 1px solid #dbe6dd;
        border-radius: 9px;
        background: #fff;
        padding: 11px 12px;
        color: #253e2c;
        font-family: inherit;
        font-size: 13px;
    }

    .zone-editor-field textarea {
        min-height: 100px;
        resize: vertical;
        line-height: 1.6;
    }

    .zone-editor-field input:focus,
    .zone-editor-field select:focus,
    .zone-editor-field textarea:focus {
        outline: 2px solid rgba(25, 139, 70, .2);
        border-color: #198b46;
    }

    .zone-editor-field small {
        color: #758378;
        font-size: 11px;
        line-height: 1.5;
    }

    .zone-editor-coordinates {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .zone-editor-error {
        font-size: 12px;
        color: #c62828;
    }

    .zone-editor-alert {
        padding: 13px 15px;
        border-radius: 9px;
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #991b1b;
        font-size: 12px;
        line-height: 1.6;
        margin-bottom: 18px;
    }

    .zone-editor-notice {
        padding: 13px 15px;
        border-radius: 9px;
        border: 1px solid #d1e6d6;
        background: #eef7f0;
        color: #285c3b;
        font-size: 12px;
        line-height: 1.7;
        margin-bottom: 18px;
    }

    .zone-editor-actions {
        padding: 17px 18px;
        border-top: 1px solid #edf0ee;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .zone-editor-save {
        border: 1px solid #198b46;
        background: #198b46;
        color: #fff;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 700;
        padding: 12px 19px;
        cursor: pointer;
    }

    .zone-editor-save:hover {
        background: #13733a;
    }

    .zone-editor-cancel {
        padding: 11px 16px;
        border-radius: 9px;
        text-decoration: none;
        border: 1px solid #dce6df;
        color: #425a48;
        font-size: 13px;
    }

    #zoneEditorMap {
        height: 570px;
        background: #e9f0ed;
        width: 100%;
    }

    .zone-editor-map-note {
        padding: 15px 18px;
        font-size: 12px;
        color: #627266;
        line-height: 1.7;
        border-top: 1px solid #edf0ee;
    }

    .zone-editor-map-error {
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-align: center;
        padding: 20px;
        box-sizing: border-box;
        color: #65776a;
        font-size: 13px;
    }

    @media (max-width: 1100px) {
        .zone-editor-layout {
            grid-template-columns: 1fr;
        }

        #zoneEditorMap {
            height: 450px;
        }
    }

    @media (max-width: 700px) {
        .geofence-editor-page .content {
            padding: 14px;
        }

        .zone-editor-coordinates {
            grid-template-columns: 1fr;
        }

        #zoneEditorMap {
            height: 350px;
        }

        .zone-editor-actions {
            justify-content: stretch;
        }

        .zone-editor-actions > * {
            flex: 1;
            text-align: center;
        }
    }
</style>
@endpush

@section('content')

@php
    $isEditing = $mode === 'edit';

    $zoneName = old('zone_name', $zone->zone_name ?? '');

    $zoneType = old('zone_type', $zone->zone_type ?? 'Safe');

    $latitude = old(
        'latitude',
        $zone->latitude ?? 10.3157
    );

    $longitude = old(
        'longitude',
        $zone->longitude ?? 123.8854
    );

    $radius = old(
        'radius_meters',
        $zone->radius_meters ?? 300
    );

    $alertMessage = old(
        'alert_message',
        $zone->alert_message ?? ''
    );

    $touristId = old(
        'tourist_id',
        $zone->tourist_id ?? ''
    );
@endphp

<!-- PAGE HEADER -->

<div class="zone-editor-header">

    <div>
        <h1>
            {{ $isEditing ? 'Edit Geofence Zone' : 'Add Geofence Zone' }}
        </h1>

        <div class="zone-editor-subtitle">
            Configure a geographic boundary, classification,
            radius, and safety message for CityPulse.
        </div>
    </div>

    <a
        href="{{ route('admin.geofence-zones.index') }}"
        class="zone-editor-back"
    >
        Back to Geofence Zones
    </a>

</div>

<div class="zone-editor-notice">
    Changes are saved to the shared CityPulse geofence
    table. The Android-facing database view reflects
    updates automatically, but the Tourist App must
    reload its zone data to display those changes.
</div>

@if ($errors->any())
    <div class="zone-editor-alert" role="alert">
        <strong>Please correct the following:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    id="zoneEditorForm"
    method="POST"
    action="{{ $isEditing
        ? route('admin.geofence-zones.update', $zone->zone_id)
        : route('admin.geofence-zones.store') }}"
>

    @csrf

    @if ($isEditing)
        @method('PUT')
    @endif

    <div class="zone-editor-layout">

        <!-- ZONE DETAILS -->

        <section class="zone-editor-card">

            <div class="zone-editor-card-header">

                <h2>
                    Zone Configuration
                </h2>

                <p>
                    Fill in the required information.
                    You can click the map to select the zone center.
                </p>

            </div>

            <div class="zone-editor-fields">

                <div class="zone-editor-field">

                    <label for="zone_name">
                        Zone Name *
                    </label>

                    <input
                        type="text"
                        name="zone_name"
                        id="zone_name"
                        value="{{ $zoneName }}"
                        maxlength="150"
                        required
                    >

                    @error('zone_name')
                        <span class="zone-editor-error">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

                <div class="zone-editor-field">

                    <label for="zone_type">
                        Zone Classification *
                    </label>

                    <select
                        name="zone_type"
                        id="zone_type"
                        required
                    >
                        @foreach ($zoneTypes as $type)

                            <option
                                value="{{ $type }}"
                                {{ $zoneType === $type ? 'selected' : '' }}
                            >
                                {{ $type }}
                            </option>

                        @endforeach
                    </select>

                    <small>
                        Classifications are configured zone
                        labels, not automatically generated
                        Random Forest predictions.
                    </small>

                </div>

                <div class="zone-editor-field">

                    <label for="tourist_id">
                        Associated Tourist Spot
                    </label>

                    <select
                        name="tourist_id"
                        id="tourist_id"
                    >
                        <option value="">
                            No associated tourist spot
                        </option>

                        @foreach ($touristSpots as $spot)

                            <option
                                value="{{ $spot->tourist_id }}"
                                {{ (string) $touristId ===
                                    (string) $spot->tourist_id
                                    ? 'selected'
                                    : '' }}
                            >
                                {{ $spot->name }}
                            </option>

                        @endforeach

                    </select>

                    <small>
                        Optional. Select a tourist spot if this
                        geofence is associated with a destination.
                    </small>

                </div>

                <div class="zone-editor-coordinates">

                    <div class="zone-editor-field">

                        <label for="latitude">
                            Latitude *
                        </label>

                        <input
                            type="number"
                            step="any"
                            min="-90"
                            max="90"
                            name="latitude"
                            id="latitude"
                            value="{{ $latitude }}"
                            required
                        >

                    </div>

                    <div class="zone-editor-field">

                        <label for="longitude">
                            Longitude *
                        </label>

                        <input
                            type="number"
                            step="any"
                            min="-180"
                            max="180"
                            name="longitude"
                            id="longitude"
                            value="{{ $longitude }}"
                            required
                        >

                    </div>

                </div>

                <div class="zone-editor-field">

                    <small>
                        Click the map to select coordinates,
                        or enter latitude and longitude manually.
                        Both values use WGS 84 / SRID 4326.
                    </small>

                </div>

                <div class="zone-editor-field">

                    <label for="radius_meters">
                        Radius (meters) *
                    </label>

                    <input
                        type="number"
                        name="radius_meters"
                        id="radius_meters"
                        value="{{ $radius }}"
                        min="1"
                        max="50000"
                        step="1"
                        required
                    >

                    <small>
                        The circular boundary will be previewed
                        on the map. Allowed range: 1–50,000 meters.
                    </small>

                </div>

                <div class="zone-editor-field">

                    <label for="alert_message">
                        Safety / Zone Alert Message *
                    </label>

                    <textarea
                        name="alert_message"
                        id="alert_message"
                        maxlength="1000"
                        required
                    >{{ $alertMessage }}</textarea>

                    <small>
                        This message is stored with the zone
                        and may be displayed by supported
                        tourist geofence features.
                    </small>

                </div>

            </div>

            <div class="zone-editor-actions">

                <a
                    class="zone-editor-cancel"
                    href="{{ route('admin.geofence-zones.index') }}"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="zone-editor-save"
                >
                    {{ $isEditing
                        ? 'Save Changes'
                        : 'Create Zone' }}
                </button>

            </div>

        </section>


        <!-- GOOGLE MAP EDITOR -->

        <section class="zone-editor-card">

            <div class="zone-editor-card-header">

                <h2>
                    Geofence Map Preview
                </h2>

                <p>
                    Click anywhere on the map to move the
                    zone center. Adjust the radius to preview
                    the circular boundary.
                </p>

            </div>

            <div id="zoneEditorMap"></div>

            <div class="zone-editor-map-note">

                Green = Safe, Amber = Medium Risk,
                Red = High Risk, and Blue = Tourist.

                The preview does not change the Supabase
                database until you save the form.

            </div>

        </section>

    </div>

</form>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const radiusInput = document.getElementById('radius_meters');
    const typeInput = document.getElementById('zone_type');
    const mapElement = document.getElementById('zoneEditorMap');

    const colors = {
        'Safe': '#1B8E3E',
        'Medium Risk': '#F59E0B',
        'High Risk': '#D32F2F',
        'Tourist': '#2563EB'
    };

    let map = null;
    let circle = null;
    let marker = null;
    let markerLibrary = null;

    function getValidCenter() {
        const lat = Number(latitudeInput.value);
        const lng = Number(longitudeInput.value);

        if (
            latitudeInput.value === '' ||
            longitudeInput.value === '' ||
            !Number.isFinite(lat) ||
            !Number.isFinite(lng) ||
            lat < -90 ||
            lat > 90 ||
            lng < -180 ||
            lng > 180
        ) {
            return null;
        }

        return { lat: lat, lng: lng };
    }

    function getValidRadius() {
        const radius = Number(radiusInput.value);

        return (
            Number.isInteger(radius) &&
            radius >= 1 &&
            radius <= 50000
        ) ? radius : null;
    }

    function refreshPreview(recenter) {
        if (!map || !circle) {
            return;
        }

        const center = getValidCenter();
        const radius = getValidRadius();

        if (!center || radius === null) {
            circle.setVisible(false);

            if (marker) {
                marker.position = null;
            }

            return;
        }

        const color = colors[typeInput.value] || '#64748B';

        circle.setVisible(true);
        circle.setCenter(center);
        circle.setRadius(radius);

        circle.setOptions({
            strokeColor: color,
            fillColor: color
        });

        if (marker) {
            marker.position = center;
        }

        if (recenter) {
            map.panTo(center);
        }
    }

    window.initZoneEditorMap = async function () {
        if (!mapElement) {
            return;
        }

        try {
            const { Map, Circle } =
                await google.maps.importLibrary('maps');

            const center = getValidCenter() || {
                lat: 10.3157,
                lng: 123.8854
            };

            map = new Map(mapElement, {
                center: center,
                zoom: 13,
                mapId: 'DEMO_MAP_ID',
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
                zoomControl: true,
                cameraControl: false,
                gestureHandling: 'greedy'
            });

            circle = new Circle({
                map: map,
                center: center,
                radius: getValidRadius() || 300,
                strokeColor: colors[typeInput.value] || '#64748B',
                strokeOpacity: .9,
                strokeWeight: 2,
                fillColor: colors[typeInput.value] || '#64748B',
                fillOpacity: .17,
                editable: false,
                draggable: false
            });

            const { AdvancedMarkerElement } =
                await google.maps.importLibrary('marker');

            markerLibrary = AdvancedMarkerElement;

            marker = new markerLibrary({
                map: map,
                position: center
            });

            map.addListener('click', function (event) {
                if (!event.latLng) {
                    return;
                }

                latitudeInput.value =
                    event.latLng.lat().toFixed(7);

                longitudeInput.value =
                    event.latLng.lng().toFixed(7);

                refreshPreview(false);
            });

            latitudeInput.addEventListener(
                'input',
                function () {
                    refreshPreview(true);
                }
            );

            longitudeInput.addEventListener(
                'input',
                function () {
                    refreshPreview(true);
                }
            );

            radiusInput.addEventListener(
                'input',
                function () {
                    refreshPreview(false);
                }
            );

            typeInput.addEventListener(
                'change',
                function () {
                    refreshPreview(false);
                }
            );

            refreshPreview(false);

        } catch (error) {
            console.error('CityPulse Geofence Editor:', error);
            window.showZoneEditorMapError();
        }
    };

    window.showZoneEditorMapError = function () {
        if (!mapElement) {
            return;
        }

        mapElement.replaceChildren();

        const box = document.createElement('div');
        box.className = 'zone-editor-map-error';

        const heading = document.createElement('strong');
        heading.textContent = 'Google Maps could not be loaded.';

        const details = document.createElement('span');
        details.textContent =
            'You may still enter valid coordinates manually.';

        box.append(heading, details);
        mapElement.appendChild(box);
    };

})();
</script>

@if (filled(env('GOOGLE_MAPS_API_KEY')))
    <script
        async
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&loading=async&callback=initZoneEditorMap&v=weekly"
        onerror="showZoneEditorMapError()"
    ></script>
@else
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.showZoneEditorMapError();
        });
    </script>
@endif
@endpush
