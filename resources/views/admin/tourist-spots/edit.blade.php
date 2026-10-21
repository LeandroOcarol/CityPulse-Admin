@extends('admin.layouts.app')

@section('title', 'CityPulse | Edit Tourist Spot')
@section('body-class', 'tourist-spot-edit-page')

@push('styles')
    <link rel="stylesheet" href="/css/pages/tourist-spot-create.css">
@endpush

@section('content')

    <!-- Page Header -->

    <div class="spot-create-header">

        <div class="spot-create-heading">

            <a
                href="{{ route('admin.tourist-spots.show', $spot->tourist_id) }}"
                class="spot-create-back"
            >
                Back to Tourist Spot
            </a>

            <h1>
                Edit Tourist Spot
            </h1>

        </div>


        <div class="spot-create-header-actions">

            <a
                href="{{ route('admin.tourist-spots.show', $spot->tourist_id) }}"
                class="spot-create-cancel-button"
            >
                Cancel
            </a>

            <button
                type="submit"
                form="tourist-spot-edit-form"
                class="spot-create-save-button"
            >
                Save Changes
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
        id="tourist-spot-edit-form"
        method="POST"
        action="{{ route('admin.tourist-spots.update', $spot->tourist_id) }}"
        class="spot-create-form"
    >

        @csrf
        @method('PUT')


        <!-- Information -->

        <section class="spot-form-card">

            <div class="spot-form-card-header">
                <h2>Information</h2>
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
                            value="{{ old('name', $spot->name) }}"
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
                            value="{{ old('category', $spot->category) }}"
                            list="category-list"
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
                        required
                    >{{ old('description', $spot->description) }}</textarea>

                </div>


                <div class="spot-form-group">

                    <label for="history">
                        History
                    </label>

                    <textarea
                        id="history"
                        name="history"
                        rows="4"
                    >{{ old('history', $spot->history) }}</textarea>

                </div>

            </div>

        </section>


        <!-- Location -->

        <section class="spot-form-card">

            <div class="spot-form-card-header">
                <h2>Location</h2>
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
                            value="{{ old('latitude', $spot->latitude) }}"
                            step="0.00000001"
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
                            value="{{ old('longitude', $spot->longitude) }}"
                            step="0.00000001"
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
                        value="{{ old('address', $spot->address) }}"
                    >

                </div>

            </div>

        </section>


        <!-- Hours -->

        <section class="spot-form-card">

            <div class="spot-form-card-header">
                <h2>Hours</h2>
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
                        value="{{ old('opening_hours', $spot->opening_hours) }}"
                    >

                </div>

            </div>

        </section>


        <!-- Media -->

        <section class="spot-form-card">

            <div class="spot-form-card-header">
                <h2>Media</h2>
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
                        value="{{ old('image_url', $spot->image_url) }}"
                        placeholder="https://..."
                    >

                </div>

            </div>

        </section>


        <!-- Bottom Actions -->

        <div class="spot-form-bottom-actions">

            <a
                href="{{ route('admin.tourist-spots.show', $spot->tourist_id) }}"
                class="spot-create-cancel-button"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="spot-create-save-button"
            >
                Save Changes
            </button>

        </div>

    </form>

@endsection