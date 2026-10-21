@extends('admin.layouts.app')

@section('title', 'CityPulse | Add Crime Record')
@section('body-class', 'crime-record-create-page')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/pages/crime-record-create.css') }}"
    >
@endpush

@section('content')

    <!-- Header -->

    <div class="crime-create-header">

        <div>

            <a
                href="{{ route('admin.crime-records.index') }}"
                class="crime-create-back"
            >
                Back to Crime Records
            </a>

            <h1>
                Add Crime Record
            </h1>

        </div>


        <div class="crime-create-header-actions">

            <a
                href="{{ route('admin.crime-records.index') }}"
                class="crime-create-cancel"
            >
                Cancel
            </a>

            <button
                type="submit"
                form="crimeRecordForm"
                class="crime-create-save"
            >
                Save Record
            </button>

        </div>

    </div>


    <!-- Validation -->

    @if($errors->any())

        <div class="crime-create-errors">

            <strong>
                Please correct the following:
            </strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <!-- Form -->

    <form
        id="crimeRecordForm"
        method="POST"
        action="{{ route('admin.crime-records.store') }}"
        class="crime-create-form"
    >
        @csrf


        <!-- Crime Information -->

        <section class="crime-create-card">

            <div class="crime-create-card-header">
                <h2>Crime Information</h2>
            </div>


            <div class="crime-create-card-body crime-create-grid">

                <div class="crime-create-field">

                    <label for="type_id">
                        Crime Type
                    </label>

                    <select
                        id="type_id"
                        name="type_id"
                        required
                    >

                        <option value="">
                            Select crime type
                        </option>

                        @foreach($crimeTypes as $crimeType)

                            <option
                                value="{{ $crimeType->type_id }}"
                                {{ old('type_id') == $crimeType->type_id ? 'selected' : '' }}
                            >
                                {{ $crimeType->incident_name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="crime-create-field">

                    <label for="severity">
                        Severity
                    </label>

                    <select
                        id="severity"
                        name="severity"
                        required
                    >

                        <option value="">
                            Select severity
                        </option>

                        <option
                            value="Low"
                            {{ old('severity') === 'Low' ? 'selected' : '' }}
                        >
                            Low
                        </option>

                        <option
                            value="Medium"
                            {{ old('severity') === 'Medium' ? 'selected' : '' }}
                        >
                            Medium
                        </option>

                        <option
                            value="High"
                            {{ old('severity') === 'High' ? 'selected' : '' }}
                        >
                            High
                        </option>

                    </select>

                </div>


                <div class="crime-create-field">

                    <label for="crime_date">
                        Crime Date
                    </label>

                    <input
                        type="date"
                        id="crime_date"
                        name="crime_date"
                        value="{{ old('crime_date') }}"
                        required
                    >

                </div>


                <div class="crime-create-field">

                    <label for="crime_count">
                        Crime Count
                    </label>

                    <input
                        type="number"
                        id="crime_count"
                        name="crime_count"
                        value="{{ old('crime_count', 1) }}"
                        min="1"
                        required
                    >

                </div>


                <div class="crime-create-field crime-create-field-full">

                    <label for="source">
                        Source
                    </label>

                    <input
                        type="text"
                        id="source"
                        name="source"
                        value="{{ old('source') }}"
                        placeholder="Example: Cebu City Police Office (CCPO)"
                        required
                    >

                </div>

            </div>

        </section>


        <!-- Location -->

        <section class="crime-create-card">

            <div class="crime-create-card-header">
                <h2>Location</h2>
            </div>


            <div class="crime-create-card-body crime-create-grid">

                <div class="crime-create-field crime-create-field-full">

                    <label for="zone_id">
                        Geofence Zone
                    </label>

                    <select
                        id="zone_id"
                        name="zone_id"
                    >

                        <option value="">
                            Unassigned
                        </option>

                        @foreach($zones as $zone)

                            <option
                                value="{{ $zone->zone_id }}"
                                {{ old('zone_id') == $zone->zone_id ? 'selected' : '' }}
                            >
                                {{ $zone->zone_name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="crime-create-field">

                    <label for="latitude">
                        Latitude
                    </label>

                    <input
                        type="number"
                        id="latitude"
                        name="latitude"
                        value="{{ old('latitude') }}"
                        step="any"
                        min="-90"
                        max="90"
                        placeholder="10.315700"
                        required
                    >

                </div>


                <div class="crime-create-field">

                    <label for="longitude">
                        Longitude
                    </label>

                    <input
                        type="number"
                        id="longitude"
                        name="longitude"
                        value="{{ old('longitude') }}"
                        step="any"
                        min="-180"
                        max="180"
                        placeholder="123.885400"
                        required
                    >

                </div>

            </div>

        </section>


        <!-- Bottom Actions -->

        <div class="crime-create-bottom-actions">

            <a
                href="{{ route('admin.crime-records.index') }}"
                class="crime-create-cancel"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="crime-create-save"
            >
                Save Record
            </button>

        </div>

    </form>

@endsection