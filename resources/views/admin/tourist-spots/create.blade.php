@extends('admin.layouts.app')

@section('title', 'CityPulse | Add Tourist Spot')
@section('body-class', 'tourist-spot-create-page')

@push('styles')
    <link rel="stylesheet" href="/css/pages/tourist-spot-create.css">
@endpush

@section('content')

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


    <form
        id="tourist-spot-form"
        method="POST"
        action="{{ route('admin.tourist-spots.store') }}"
        class="spot-create-form"
        enctype="multipart/form-data"
    >

        @csrf


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
                        value="{{ old('opening_hours') }}"
                        placeholder="e.g. 8:00 AM - 5:00 PM"
                    >

                </div>

            </div>

        </section>


        <section class="spot-form-card">

            <div class="spot-form-card-header">
                <h2>Media</h2>
            </div>

            <div class="spot-form-card-body">

                <div class="spot-form-group">

                    <label>
                        Tourist Spot Photo
                    </label>

                    <input
                        id="spot_photo"
                        type="file"
                        name="spot_photo"
                        accept="image/jpeg,image/png,image/webp"
                        hidden
                    >

                    <div>

                        <label
                            for="spot_photo"
                            class="spot-photo-button"
                        >
                            Add Photo
                        </label>

                    </div>

                    <small>
                        JPG, JPEG, PNG, or WEBP. Maximum file size: 10 MB.
                    </small>

                </div>


                <div
                    id="photo-preview-container"
                    class="spot-form-group"
                    hidden
                >

                    <label>
                        Photo Preview
                    </label>

                    <div>

                        <img
                            id="photo-preview"
                            src=""
                            alt="Tourist spot photo preview"
                            width="240"
                        >

                    </div>

                </div>


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

                    <small id="image-url-helper">
                        Optional. Use this if you prefer an existing online image instead of uploading a photo.
                    </small>

                </div>

            </div>

        </section>


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


@push('scripts')

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const photoInput =
                    document.getElementById('spot_photo');

                const imageUrlInput =
                    document.getElementById('image_url');

                const imageUrlHelper =
                    document.getElementById('image-url-helper');

                const previewContainer =
                    document.getElementById(
                        'photo-preview-container'
                    );

                const preview =
                    document.getElementById(
                        'photo-preview'
                    );


                function showPreview(source) {

                    if (!source) {

                        preview.src = '';

                        previewContainer.hidden = true;

                        return;

                    }

                    preview.src = source;

                    previewContainer.hidden = false;

                }


                function enableImageUrl() {

                    imageUrlInput.disabled = false;

                    imageUrlHelper.textContent =
                        'Optional. Use this if you prefer an existing online image instead of uploading a photo.';

                }


                function disableImageUrl() {

                    imageUrlInput.value = '';

                    imageUrlInput.disabled = true;

                    imageUrlHelper.textContent =
                        'Image URL is disabled because a local photo has been selected.';

                }


                photoInput.addEventListener(
                    'change',
                    function () {

                        const file =
                            photoInput.files[0];


                        if (!file) {

                            enableImageUrl();

                            showPreview(
                                imageUrlInput.value.trim()
                            );

                            return;

                        }


                        disableImageUrl();


                        const reader =
                            new FileReader();


                        reader.onload =
                            function (event) {

                                showPreview(
                                    event.target.result
                                );

                            };


                        reader.readAsDataURL(file);

                    }
                );


                imageUrlInput.addEventListener(
                    'input',
                    function () {

                        if (
                            photoInput.files.length > 0
                        ) {
                            return;
                        }


                        showPreview(
                            imageUrlInput.value.trim()
                        );

                    }
                );


                if (
                    imageUrlInput.value.trim() !== ''
                ) {

                    showPreview(
                        imageUrlInput.value.trim()
                    );

                }

            }
        );

    </script>

@endpush