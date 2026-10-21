@extends('admin.layouts.app')

@section('title', 'CityPulse | Add Tourist Spot')
@section('body-class', 'tourist-spot-create-page')

@push('styles')
    <link rel="stylesheet" href="/css/pages/tourist-spot-create.css">
@endpush

@section('content')

    <!-- Page Header -->

    <div class="spot-create-header">

        <div class="spot-create-heading">

            <a
                href="{{ route('admin.tourist-spots.index') }}"
                class="spot-create-back"
            >
                Back to Tourist Spots
            </a>

            <h1>
                Add Tourist Spot
            </h1>

        </div>


        <div class="spot-create-header-actions">

            <a
                href="{{ route('admin.tourist-spots.index') }}"
                class="spot-create-cancel-button"
            >
                Cancel
            </a>

            <button
                type="submit"
                form="tourist-spot-form"
                class="spot-create-save-button"
            >
                Save Spot
            </button>

        </div>

    </div>


    <!-- Validation Errors -->

    @if($errors->any())

        <div class="spot-form-errors">

            <strong>
                Check the highlighted fields.
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
        id="tourist-spot-form"
        method="POST"
        action="{{ route('admin.tourist-spots.store') }}"
        class="spot-create-form"
    >

        @csrf


        <!-- Basic Information -->

        <section class="spot-form-card">

            <div class="spot-form-card-header">

                <h2>
                    Information
                </h2>

            </div>


            <div class="spot-form-card-body">

                <div class="spot-form-grid">

                    <div class="spot-form-group">

                        <label for="name">
                            Spot Name
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="e.g. Fort San Pedro"
                            required
                        >

                    </div>


                    <div class="spot-form-group">

                        <label for="category">
                            Category
                        </label>

                        <input
                            id="category"
                            type="text"
                            name="category"
                            value="{{ old('category') }}"
                            list="category-list"
                            placeholder="e.g. Historical Landmark"
                            required
                        >

                        <datalist id="category-list">

                            @foreach($categories as $categoryOption)

                                <option value="{{ $categoryOption }}"></option>

                            @endforeach

                        </datalist>

                    </div>

                </div>


                <div class="spot-form-group">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Enter a short description"
                        required
                    >{{ old('description') }}</textarea>

                </div>


                <div class="spot-form-group">

                    <label for="history">
                        History
                    </label>

                    <textarea
                        id="history"
                        name="history"
                        rows="4"
                        placeholder="Enter historical information"
                    >{{ old('history') }}</textarea>

                </div>

            </div>

        </section>


        <!-- Location -->

        <section class="spot-form-card">

            <div class="spot-form-card-header">

                <h2>
                    Location
                </h2>

            </div>


            <div class="spot-form-card-body">

                <div class="spot-form-grid">

                    <div class="spot-form-group">

                        <label for="latitude">
                            Latitude
                        </label>

                        <input
                            id="latitude"
                            type="number"
                            name="latitude"
                            value="{{ old('latitude') }}"
                            step="0.00000001"
                            placeholder="10.315699"
                            required
                        >

                    </div>


                    <div class="spot-form-group">

                        <label for="longitude">
                            Longitude
                        </label>

                        <input
                            id="longitude"
                            type="number"
                            name="longitude"
                            value="{{ old('longitude') }}"
                            step="0.00000001"
                            placeholder="123.885437"
                            required
                        >

                    </div>

                </div>


                <div class="spot-form-group">

                    <label for="address">
                        Address
                    </label>

                    <input
                        id="address"
                        type="text"
                        name="address"
                        value="{{ old('address') }}"
                        placeholder="Street, barangay, or area"
                    >

                </div>

            </div>

        </section>


        <!-- Opening Hours -->

        <section class="spot-form-card">

            <div class="spot-form-card-header">

                <h2>
                    Hours
                </h2>

            </div>


            <div class="spot-form-card-body">

                <div class="spot-form-group">

                    <label for="opening_hours">
                        Opening Hours
                    </label>

                    <input
                        id="opening_hours"
                        type="text"
                        name="opening_hours"
                        value="{{ old('opening_hours') }}"
                        placeholder="e.g. 8:00 AM - 5:00 PM"
                    >

                </div>

            </div>

        </section>


        <!-- Media -->

        <section class="spot-form-card">

            <div class="spot-form-card-header">

                <h2>
                    Media
                </h2>

            </div>


            <div class="spot-form-card-body">

                <div class="spot-form-group">

                    <label for="image_url">
                        Image URL
                    </label>

                    <input
                        id="image_url"
                        type="url"
                        name="image_url"
                        value="{{ old('image_url') }}"
                        placeholder="https://..."
                    >

                </div>

            </div>

        </section>


        <!-- Bottom Actions -->

        <div class="spot-form-bottom-actions">

            <a
                href="{{ route('admin.tourist-spots.index') }}"
                class="spot-create-cancel-button"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="spot-create-save-button"
            >
                Save Spot
            </button>

        </div>

    </form>

@endsection