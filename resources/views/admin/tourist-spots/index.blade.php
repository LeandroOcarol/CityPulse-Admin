@extends('admin.layouts.app')

@section('title', 'CityPulse | Tourist Spots')
@section('body-class', 'tourist-spots-page')

@push('styles')
    <link rel="stylesheet" href="/css/pages/tourist-spots.css">
@endpush

@section('content')

    <!-- Page Header -->

    <div class="spots-header-row">

        <h1>
            Tourist Spots
        </h1>

        <a
            href="{{ route('admin.tourist-spots.create') }}"
            class="spots-add-button"
        >
            Add Spot
        </a>

    </div>


    <!-- Success Message -->

    @if(session('success'))

        <div class="success-message">
            {{ session('success') }}
        </div>

    @endif


    <!-- Summary -->

    <section class="spots-summary-grid">

        <div class="spots-summary-card">

            <div class="spots-summary-content">

                <span class="spots-summary-label">
                    Tourist Spots
                </span>

                <strong class="spots-summary-value">
                    {{ number_format($totalSpots ?? 0) }}
                </strong>

            </div>

        </div>


        <div class="spots-summary-card">

            <div class="spots-summary-content">

                <span class="spots-summary-label">
                    Categories
                </span>

                <strong class="spots-summary-value">
                    {{ number_format($totalCategories ?? 0) }}
                </strong>

            </div>

        </div>

    </section>


    <!-- Filters -->

    <section class="spots-filter-card">

        <form
            method="GET"
            action="{{ route('admin.tourist-spots.index') }}"
            class="spots-filter-form"
        >

            <div class="spots-filter-main">

                <input
                    type="search"
                    name="search"
                    value="{{ $search ?? '' }}"
                    placeholder="Search spots"
                    class="spots-search-input"
                >

            </div>


            <div class="spots-filter-side">

                <select
                    name="category"
                    class="spots-category-select"
                >

                    <option
                        value="all"
                        {{ ($category ?? 'all') === 'all' ? 'selected' : '' }}
                    >
                        All Categories
                    </option>

                    @foreach($categories as $categoryOption)

                        <option
                            value="{{ $categoryOption }}"
                            {{ ($category ?? 'all') === $categoryOption ? 'selected' : '' }}
                        >
                            {{ $categoryOption }}
                        </option>

                    @endforeach

                </select>


                <button
                    type="submit"
                    class="spots-filter-button"
                >
                    Filter
                </button>


                @if(
                    ($search ?? '') !== '' ||
                    ($category ?? 'all') !== 'all'
                )

                    <a
                        href="{{ route('admin.tourist-spots.index') }}"
                        class="spots-clear-button"
                    >
                        Clear
                    </a>

                @endif

            </div>

        </form>

    </section>


    <!-- Spot Directory -->

    <section class="spots-table-card">

        <div class="spots-table-header">

            <div>

                <h2>
                    Spot Directory
                </h2>

                <span>
                    {{ $touristSpots->total() }} spots
                </span>

            </div>

        </div>


        <div class="spots-table-wrap">

            <table class="spots-table">

                <thead>

                    <tr>
                        <th>Spot</th>
                        <th>Category</th>
                        <th>Coordinates</th>
                        <th>Hours</th>
                        <th>Added</th>
                        <th>Actions</th>
                    </tr>

                </thead>


                <tbody>

                    @forelse($touristSpots as $spot)

                        <tr>

                            <td>

                                <div class="spot-cell">

                                    <div class="spot-thumbnail">

                                        @if(!empty($spot->image_url))

                                            <img
                                                src="{{ $spot->image_url }}"
                                                alt="{{ $spot->name }}"
                                            >

                                        @else

                                            <span>
                                                {{
                                                    strtoupper(
                                                        substr(
                                                            $spot->name ?? 'S',
                                                            0,
                                                            1
                                                        )
                                                    )
                                                }}
                                            </span>

                                        @endif

                                    </div>


                                    <div class="spot-cell-info">

                                        <strong>
                                            {{ $spot->name }}
                                        </strong>

                                        @if(!empty($spot->address))

                                            <span>
                                                {{
                                                    \Illuminate\Support\Str::limit(
                                                        $spot->address,
                                                        45
                                                    )
                                                }}
                                            </span>

                                        @elseif(!empty($spot->description))

                                            <span>
                                                {{
                                                    \Illuminate\Support\Str::limit(
                                                        $spot->description,
                                                        45
                                                    )
                                                }}
                                            </span>

                                        @endif

                                    </div>

                                </div>

                            </td>


                            <td>

                                <span class="spot-category-badge">
                                    {{ $spot->category ?? '—' }}
                                </span>

                            </td>


                            <td>

                                @if(
                                    $spot->latitude !== null &&
                                    $spot->longitude !== null
                                )

                                    <div class="spot-coordinate-group">

                                        <span>
                                            {{ number_format($spot->latitude, 6) }}
                                        </span>

                                        <span>
                                            {{ number_format($spot->longitude, 6) }}
                                        </span>

                                    </div>

                                @else

                                    —

                                @endif

                            </td>


                            <td>
                                {{ $spot->opening_hours ?? '—' }}
                            </td>


                            <td>

                                @if(!empty($spot->created_at))

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

                                <div class="spot-action-group">

                                    <a
                                        href="{{ route('admin.tourist-spots.show', $spot->tourist_id) }}"
                                        class="spot-action-button spot-view-button"
                                    >
                                        View
                                    </a>

                                    <a
                                        href="{{ route('admin.tourist-spots.edit', $spot->tourist_id) }}"
                                        class="spot-action-button spot-edit-button"
                                    >
                                        Edit
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="spots-empty-state"
                            >
                                No tourist spots found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <!-- Pagination -->

        <div class="spots-pagination">

            <div class="spots-pagination-info">

                Showing

                <strong>
                    {{ $touristSpots->firstItem() ?? 0 }}
                </strong>

                –

                <strong>
                    {{ $touristSpots->lastItem() ?? 0 }}
                </strong>

                of

                <strong>
                    {{ $touristSpots->total() }}
                </strong>

            </div>


            <div class="spots-pagination-controls">

                @if($touristSpots->onFirstPage())

                    <span class="spots-page-button disabled">
                        Previous
                    </span>

                @else

                    <a
                        href="{{ $touristSpots->previousPageUrl() }}"
                        class="spots-page-button"
                    >
                        Previous
                    </a>

                @endif


                <span class="spots-page-current">

                    {{ $touristSpots->currentPage() }}
                    /
                    {{ $touristSpots->lastPage() }}

                </span>


                @if($touristSpots->hasMorePages())

                    <a
                        href="{{ $touristSpots->nextPageUrl() }}"
                        class="spots-page-button"
                    >
                        Next
                    </a>

                @else

                    <span class="spots-page-button disabled">
                        Next
                    </span>

                @endif

            </div>

        </div>

    </section>

@endsection