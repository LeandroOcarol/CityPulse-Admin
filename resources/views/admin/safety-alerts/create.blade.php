@extends('admin.layouts.app')

@section('title', 'CityPulse | Add Safety Alert')
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
                href="{{ route('admin.safety-alerts.index') }}"
                class="alert-form-back"
            >
                Back to Safety Alerts
            </a>

            <h1>
                Add Safety Alert
            </h1>

        </div>

    </div>


    <form
        method="POST"
        action="{{ route('admin.safety-alerts.store') }}"
        class="alert-form"
    >

        @csrf


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

                            <option value="">
                                Select zone
                            </option>

                            @foreach($zones as $zone)

                                <option
                                    value="{{ $zone->zone_id }}"
                                    {{ old('zone_id') == $zone->zone_id ? 'selected' : '' }}
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

                            <option value="">
                                Select level
                            </option>

                            @foreach(['Low', 'Medium', 'High', 'Emergency'] as $level)

                                <option
                                    value="{{ $level }}"
                                    {{ old('alert_level') === $level ? 'selected' : '' }}
                                >
                                    {{ $level }}
                                </option>

                            @endforeach

                        </select>

                        @error('alert_level')
                            <span class="alert-error">{{ $message }}</span>
                        @enderror

                    </div>


                    <div class="alert-field alert-field-full">

                        <label for="title">
                            Title
                        </label>

                        <input
                            id="title"
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            required
                        >

                        @error('title')
                            <span class="alert-error">{{ $message }}</span>
                        @enderror

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
                        >{{ old('message') }}</textarea>

                        @error('message')
                            <span class="alert-error">{{ $message }}</span>
                        @enderror

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
                            value="{{ old('starts_at') }}"
                            required
                        >

                        @error('starts_at')
                            <span class="alert-error">{{ $message }}</span>
                        @enderror

                    </div>


                    <div class="alert-field">

                        <label for="ends_at">
                            Ends At
                        </label>

                        <input
                            id="ends_at"
                            type="datetime-local"
                            name="ends_at"
                            value="{{ old('ends_at') }}"
                        >

                        @error('ends_at')
                            <span class="alert-error">{{ $message }}</span>
                        @enderror

                        <span class="alert-helper">
                            Leave blank if the alert has no scheduled end.
                        </span>

                    </div>

                </div>

            </div>

        </section>


        <div class="alert-form-actions">

            <a
                href="{{ route('admin.safety-alerts.index') }}"
                class="alert-cancel-button"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="alert-save-button"
            >
                Add Safety Alert
            </button>

        </div>

    </form>

@endsection