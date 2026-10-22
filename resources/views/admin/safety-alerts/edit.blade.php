@extends('admin.layouts.app')

@section('title', 'CityPulse | Edit Safety Alert')
@section('body-class', 'safety-alert-form-page')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/pages/safety-alert-form.css') }}"
    >
@endpush

@section('content')

    <div class="alert-form-header">

        <div>

            <a
                href="{{ route('admin.safety-alerts.show', $alert->alert_id) }}"
                class="alert-form-back"
            >
                Back to Safety Alert
            </a>

            <h1>
                Edit Safety Alert
            </h1>

        </div>

    </div>


    <form
        method="POST"
        action="{{ route('admin.safety-alerts.update', $alert->alert_id) }}"
        class="alert-form"
    >

        @csrf
        @method('PUT')


        <section class="alert-form-card">

            <div class="alert-form-card-header">
                <h2>Alert Information</h2>
            </div>


            <div class="alert-form-card-body">

                <div class="alert-form-grid">

                    <div class="alert-field">

                        <label for="zone_id">
                            Geofence Zone
                        </label>

                        <select
                            id="zone_id"
                            name="zone_id"
                            required
                        >

                            @foreach($zones as $zone)

                                <option
                                    value="{{ $zone->zone_id }}"
                                    {{ (string) old('zone_id', $alert->zone_id) === (string) $zone->zone_id ? 'selected' : '' }}
                                >
                                    {{ $zone->zone_name }} — {{ $zone->zone_type }}
                                </option>

                            @endforeach

                        </select>

                        @error('zone_id')
                            <span class="alert-error">{{ $message }}</span>
                        @enderror

                    </div>


                    <div class="alert-field">

                        <label for="alert_level">
                            Alert Level
                        </label>

                        <select
                            id="alert_level"
                            name="alert_level"
                            required
                        >

                            @foreach(['Low', 'Medium', 'High', 'Emergency'] as $level)

                                <option
                                    value="{{ $level }}"
                                    {{ old('alert_level', $alert->alert_level) === $level ? 'selected' : '' }}
                                >
                                    {{ $level }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="alert-field alert-field-full">

                        <label for="title">
                            Title
                        </label>

                        <input
                            id="title"
                            type="text"
                            name="title"
                            value="{{ old('title', $alert->title) }}"
                            required
                        >

                    </div>


                    <div class="alert-field alert-field-full">

                        <label for="message">
                            Message
                        </label>

                        <textarea
                            id="message"
                            name="message"
                            rows="5"
                            required
                        >{{ old('message', $alert->message) }}</textarea>

                    </div>

                </div>

            </div>

        </section>


        <section class="alert-form-card">

            <div class="alert-form-card-header">
                <h2>Schedule</h2>
            </div>


            <div class="alert-form-card-body">

                <div class="alert-form-grid">

                    <div class="alert-field">

                        <label for="starts_at">
                            Starts At
                        </label>

                        <input
                            id="starts_at"
                            type="datetime-local"
                            name="starts_at"
                            value="{{ old('starts_at', \Carbon\Carbon::parse($alert->starts_at)->timezone('Asia/Manila')->format('Y-m-d\TH:i')) }}"
                            required
                        >

                    </div>


                    <div class="alert-field">

                        <label for="ends_at">
                            Ends At
                        </label>

                        <input
                            id="ends_at"
                            type="datetime-local"
                            name="ends_at"
                            value="{{ old('ends_at', !empty($alert->ends_at) ? \Carbon\Carbon::parse($alert->ends_at)->timezone('Asia/Manila')->format('Y-m-d\TH:i') : '') }}"
                        >

                    </div>

                </div>

            </div>

        </section>


        <div class="alert-form-actions">

            <a
                href="{{ route('admin.safety-alerts.show', $alert->alert_id) }}"
                class="alert-cancel-button"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="alert-save-button"
            >
                Save Changes
            </button>

        </div>

    </form>

@endsection