@extends('admin.layouts.app')

@section('title', 'CityPulse | Add Tourist Spot')

@section('body-class', 'tourist-spot-create-page')


@push('styles')

    <link
        rel="stylesheet"
        href="/css/pages/tourist-spot-create.css"
    >

@endpush


@section('content')


    <!-- =========================================
         PAGE HEADER
    ========================================== -->

    <div class="spot-create-header">

        <div>

            <a
                href="{{ route('admin.tourist-spots.index') }}"
                class="back-link"
            >
                Back to Tourist Spots
            </a>

            <h1>
                Add Tourist Spot
            </h1>

        </div>


        <div class="header-actions">

            <a
                href="{{ route('admin.tourist-spots.index') }}"
                class="cancel-button"
            >
                Cancel
            </a>

            <button
                type="submit"
                form="tourist-spot-form"
                class="save-button"
            >
                Save Spot
            </button>

        </div>

    </div>



    <!-- =========================================
         VALIDATION ERRORS
    ========================================== -->

    @if($errors->any())

        <div class="form-error-box">

            <strong>
                Check the required fields.
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



    <!-- =========================================
         FORM
    ========================================== -->

    <form
        id="tourist-spot-form"
        method="POST"
        action="{{ route('admin.tourist-spots.store') }}"
    >

        @csrf


        <!-- =====================================
             PRIMARY INFORMATION
        ====================================== -->

        <section class="spot-form-card">

            <div class="form-card-header">

                <h2>
                    Primary Information
                </h2>

            </div>


            <div class="form-card-body">

                <div class="two-column-form">

                    <div class="form-group">

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


                    <div class="form-group">

                        <label for="category">
                            Category
                        </label>

                        <input
                            id="category"
                            type="text"
                            name="category"
                            value="{{ old('category') }}"
                            list="category-list"
                            placeholder="e.g. Historical"
                            required
                        >

                        <datalist id="category-list">

                            @foreach($categories as $categoryOption)

                                <option
                                    value="{{ $categoryOption }}"
                                >
                                </option>

                            @endforeach

                        </datalist>

                    </div>

                </div>


                <div class="form-group">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Spot description"
                        required
                    >{{ old('description') }}</textarea>

                </div>


                <div class="form-group">

                    <label for="history">
                        History
                    </label>

                    <textarea
                        id="history"
                        name="history"
                        rows="4"
                        placeholder="Historical information"
                    >{{ old('history') }}</textarea>

                </div>

            </div>

        </section>



        <!-- =====================================
             LOCATION
        ====================================== -->

        <section class="spot-form-card">

            <div class="form-card-header">

                <h2>
                    Location
                </h2>

            </div>


            <div class="form-card-body">

                <div class="two-column-form">

                    <div class="form-group">

                        <label for="latitude">
                            Latitude
                        </label>

                        <input
                            id="latitude"
                            type="number"
                            name="latitude"
                            value="{{ old('latitude') }}"
                            step="0.00000001"
                            placeholder="10.00000000"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="longitude">
                            Longitude
                        </label>

                        <input
                            id="longitude"
                            type="number"
                            name="longitude"
                            value="{{ old('longitude') }}"
                            step="0.00000001"
                            placeholder="123.00000000"
                            required
                        >

                    </div>

                </div>


                <div class="location-note">

                    Coordinates are used by the CityPulse map.

                </div>

            </div>

        </section>



        <!-- =====================================
             MEDIA
        ====================================== -->

        <section class="spot-form-card">

            <div class="form-card-header">

                <h2>
                    Media
                </h2>

            </div>


            <div class="form-card-body">

                <div class="form-group">

                    <label for="image">
                        Image URL
                    </label>

                    <input
                        id="image"
                        type="url"
                        name="image"
                        value="{{ old('image') }}"
                        placeholder="https://..."
                    >

                </div>

            </div>

        </section>



        <!-- =====================================
             BOTTOM ACTIONS
        ====================================== -->

        <div class="form-bottom-actions">

            <a
                href="{{ route('admin.tourist-spots.index') }}"
                class="cancel-button"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="save-button"
            >
                Save Spot
            </button>

        </div>

    </form>

@endsection