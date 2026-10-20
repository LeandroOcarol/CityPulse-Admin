@extends('admin.layouts.app')

@section('title', 'CityPulse | Tourist Spots')

@section('body-class', 'tourist-spots-page')


@push('styles')

    <link
        rel="stylesheet"
        href="/css/pages/tourist-spots.css"
    >

@endpush


@section('content')


    <!-- =========================================
         PAGE HEADER
    ========================================== -->

    <div class="tourist-spots-header">

        <div>

            <h1>
                Tourist Spots
            </h1>

            <p>
                Manage destinations.
            </p>

        </div>


        <a
            href="{{ route('admin.tourist-spots.create') }}"
            class="add-spot-button"
        >
            Add Spot
        </a>

    </div>



    <!-- =========================================
         SUCCESS MESSAGE
    ========================================== -->

    @if(session('success'))

        <div class="success-message">
            {{ session('success') }}
        </div>

    @endif



    <!-- =========================================
         SUMMARY
    ========================================== -->

    <section class="tourist-spots-summary">

        <div class="summary-card">

            <div class="summary-label">
                Tourist Spots
            </div>

            <div class="summary-number">
                {{ number_format($totalSpots) }}
            </div>

        </div>


        <div class="summary-card">

            <div class="summary-label">
                Categories
            </div>

            <div class="summary-number">
                {{ number_format($totalCategories) }}
            </div>

        </div>

    </section>



    <!-- =========================================
         FILTERS
    ========================================== -->

    <section class="tourist-spots-filter-panel">

        <form
            method="GET"
            action="{{ route('admin.tourist-spots.index') }}"
            class="tourist-spots-filter-form"
        >

            <div class="spots-search">

                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search spots"
                >

            </div>


            <div class="spots-category-filter">

                <select name="category">

                    <option
                        value="all"
                        {{ $category === 'all' ? 'selected' : '' }}
                    >
                        All Categories
                    </option>


                    @foreach($categories as $categoryOption)

                        <option
                            value="{{ $categoryOption }}"
                            {{ $category === $categoryOption ? 'selected' : '' }}
                        >
                            {{ $categoryOption }}
                        </option>

                    @endforeach

                </select>

            </div>


            <button
                type="submit"
                class="spots-filter-button"
            >
                Filter
            </button>


            @if($search !== '' || $category !== 'all')

                <a
                    href="{{ route('admin.tourist-spots.index') }}"
                    class="spots-clear-button"
                >
                    Clear
                </a>

            @endif

        </form>

    </section>



    <!-- =========================================
         SPOT DIRECTORY
    ========================================== -->

    <section class="tourist-spots-panel">

        <div class="tourist-spots-panel-header">

            <div>

                <h2>
                    Spot Directory
                </h2>

                <p>
                    {{ $touristSpots->total() }} spots
                </p>

            </div>

        </div>


        @if($touristSpots->count() > 0)

            <div class="tourist-spots-table-wrapper">

                <table class="tourist-spots-table">

                    <thead>

                        <tr>
                            <th>Spot</th>
                            <th>Category</th>
                            <th>Coordinates</th>
                            <th>Added</th>
                            <th>Actions</th>
                        </tr>

                    </thead>


                    <tbody>

                        @foreach($touristSpots as $spot)

                            <tr>

                                <td>

                                    <div class="spot-info">

                                        <div class="spot-image">

                                            @if(!empty($spot->image))

                                                <img
                                                    src="{{ $spot->image }}"
                                                    alt=""
                                                >

                                            @else

                                                <div class="spot-image-placeholder">

                                                    {{
                                                        strtoupper(
                                                            substr(
                                                                $spot->name ?? 'S',
                                                                0,
                                                                1
                                                            )
                                                        )
                                                    }}

                                                </div>

                                            @endif

                                        </div>


                                        <div class="spot-text">

                                            <div class="spot-name">
                                                {{ $spot->name ?? 'Unnamed Spot' }}
                                            </div>

                                            <div class="spot-description">

                                                {{
                                                    \Illuminate\Support\Str::limit(
                                                        $spot->description ?? '',
                                                        55
                                                    )
                                                }}

                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <span class="spot-category-badge">
                                        {{ $spot->category ?? 'Uncategorized' }}
                                    </span>

                                </td>


                                <td>

                                    <div class="spot-coordinates">

                                        <span>
                                            {{ $spot->latitude }}
                                        </span>

                                        <span>
                                            {{ $spot->longitude }}
                                        </span>

                                    </div>

                                </td>


                                <td>

                                    @if($spot->created_at)

                                        {{
                                            \Carbon\Carbon::parse(
                                                $spot->created_at
                                            )->format('M d, Y')
                                        }}

                                    @else

                                        —

                                    @endif

                                </td>


                                <td>

                                    <div class="spot-actions">

                                        <a
                                            href="#"
                                            class="spot-action-button view"
                                        >
                                            View
                                        </a>

                                        <a
                                            href="#"
                                            class="spot-action-button edit"
                                        >
                                            Edit
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            <!-- =================================
                 PAGINATION
            ================================== -->

            <div class="tourist-spots-pagination">

                <div class="pagination-info">

                    Showing

                    <strong>
                        {{ $touristSpots->firstItem() }}
                    </strong>

                    –

                    <strong>
                        {{ $touristSpots->lastItem() }}
                    </strong>

                    of

                    <strong>
                        {{ $touristSpots->total() }}
                    </strong>

                </div>


                <div class="pagination-controls">

                    @if($touristSpots->onFirstPage())

                        <span class="pagination-button disabled">
                            Previous
                        </span>

                    @else

                        <a
                            href="{{ $touristSpots->previousPageUrl() }}"
                            class="pagination-button"
                        >
                            Previous
                        </a>

                    @endif


                    <span class="pagination-current">

                        {{ $touristSpots->currentPage() }}
                        /
                        {{ $touristSpots->lastPage() }}

                    </span>


                    @if($touristSpots->hasMorePages())

                        <a
                            href="{{ $touristSpots->nextPageUrl() }}"
                            class="pagination-button"
                        >
                            Next
                        </a>

                    @else

                        <span class="pagination-button disabled">
                            Next
                        </span>

                    @endif

                </div>

            </div>


        @else

            <div class="tourist-spots-empty">

                <h3>
                    No spots found
                </h3>

            </div>

        @endif

    </section>

@endsection