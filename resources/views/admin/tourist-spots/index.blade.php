@extends('admin.layouts.app')

@section('title', 'CityPulse | Tourist Spots')
@section('body-class', 'tourist-spots-page')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/pages/tourist-spots.css') }}"
    >
@endpush

@section('content')

    <div class="spots-header">

        <h1>
            Tourist Spots
        </h1>

        <a
            href="{{ route('admin.tourist-spots.create') }}"
            class="spots-add-button"
        >
            Add Tourist Spot
        </a>

    </div>


    @if(session('success'))

        <div class="spots-success">
            {{ session('success') }}
        </div>

    @endif


    <section class="spots-summary-grid">

        <div class="spots-summary-card">

            <span class="spots-summary-label">
                Tourist Spots
            </span>

            <strong class="spots-summary-value">
                {{ number_format($totalSpots) }}
            </strong>

        </div>


        <div class="spots-summary-card">

            <span class="spots-summary-label">
                Active Spots
            </span>

            <strong class="spots-summary-value">
                {{ number_format($activeSpots) }}
            </strong>

        </div>


        <div class="spots-summary-card">

            <span class="spots-summary-label">
                Inactive Spots
            </span>

            <strong class="spots-summary-value">
                {{ number_format($inactiveSpots) }}
            </strong>

        </div>


        <div class="spots-summary-card">

            <span class="spots-summary-label">
                Categories
            </span>

            <strong class="spots-summary-value">
                {{ number_format($totalCategories) }}
            </strong>

        </div>

    </section>


    <section class="spots-filter-card">

        <form
            method="GET"
            action="{{ route('admin.tourist-spots.index') }}"
            class="spots-filter-form"
        >

            <div class="spots-search">

                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search tourist spots"
                >

            </div>


            <div class="spots-filter-controls">

                <select name="category">

                    <option value="all">
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


                <select name="status">

                    <option
                        value="all"
                        {{ $status === 'all' ? 'selected' : '' }}
                    >
                        All Statuses
                    </option>

                    <option
                        value="active"
                        {{ $status === 'active' ? 'selected' : '' }}
                    >
                        Active
                    </option>

                    <option
                        value="inactive"
                        {{ $status === 'inactive' ? 'selected' : '' }}
                    >
                        Inactive
                    </option>

                </select>


                <button
                    type="submit"
                    class="spots-filter-button"
                >
                    Filter
                </button>


                @if(
                    $search !== '' ||
                    $category !== 'all' ||
                    $status !== 'all'
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


    <section class="spots-table-card">

        <div class="spots-table-header">

            <div>

                <h2>
                    Tourist Spot Directory
                </h2>

                <span>
                    {{ number_format($touristSpots->total()) }} records
                </span>

            </div>

        </div>


        <div class="spots-table-wrapper">

            <table class="spots-table">

                <thead>

                    <tr>
                        <th>Spot</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Coordinates</th>
                        <th>Hours</th>
                        <th>Added</th>
                        <th>Actions</th>
                    </tr>

                </thead>


                <tbody>

                    @forelse($touristSpots as $spot)

                        <tr class="{{ !$spot->is_active ? 'spots-row-inactive' : '' }}">

                            <td>

                                <div class="spots-name-cell">

                                    <strong>
                                        {{ $spot->name }}
                                    </strong>

                                    @if(!empty($spot->address))

                                        <span>
                                            {{ $spot->address }}
                                        </span>

                                    @endif

                                </div>

                            </td>


                            <td>
                                {{ $spot->category }}
                            </td>


                            <td>

                                @if($spot->is_active)

                                    <span class="spots-status spots-status-active">
                                        Active
                                    </span>

                                @else

                                    <span class="spots-status spots-status-inactive">
                                        Inactive
                                    </span>

                                @endif

                            </td>


                            <td>

                                @if(
                                    $spot->latitude !== null &&
                                    $spot->longitude !== null
                                )

                                    <div class="spots-coordinates">

                                        <span>
                                            {{ number_format($spot->latitude, 5) }}
                                        </span>

                                        <span>
                                            {{ number_format($spot->longitude, 5) }}
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

                                <div class="spots-actions">

                                    <a
                                        href="{{ route('admin.tourist-spots.show', $spot->tourist_id) }}"
                                        class="spots-view-button"
                                    >
                                        View
                                    </a>


                                    <a
                                        href="{{ route('admin.tourist-spots.edit', $spot->tourist_id) }}"
                                        class="spots-edit-button"
                                    >
                                        Edit
                                    </a>


                                    @if($spot->is_active)

                                        <form
                                            method="POST"
                                            action="{{ route('admin.tourist-spots.status', $spot->tourist_id) }}"
                                            class="spots-status-form"
                                            onsubmit="return confirm('Deactivate this tourist spot? It will remain in the database and can be reactivated later.');"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <input
                                                type="hidden"
                                                name="is_active"
                                                value="0"
                                            >

                                            <button
                                                type="submit"
                                                class="spots-deactivate-button"
                                            >
                                                Deactivate
                                            </button>

                                        </form>

                                    @else

                                        <form
                                            method="POST"
                                            action="{{ route('admin.tourist-spots.status', $spot->tourist_id) }}"
                                            class="spots-status-form"
                                            onsubmit="return confirm('Reactivate this tourist spot?');"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <input
                                                type="hidden"
                                                name="is_active"
                                                value="1"
                                            >

                                            <button
                                                type="submit"
                                                class="spots-reactivate-button"
                                            >
                                                Reactivate
                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="spots-empty"
                            >
                                No tourist spots found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


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
                    {{ number_format($touristSpots->total()) }}
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