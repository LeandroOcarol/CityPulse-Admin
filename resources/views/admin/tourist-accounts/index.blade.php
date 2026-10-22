@extends('admin.layouts.app')

@section('title', 'CityPulse | Tourist Accounts')
@section('body-class', 'tourist-accounts-page')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/pages/tourist-accounts.css') }}"
    >
@endpush

@section('content')

    <div class="tourist-header">

        <h1>
            Tourist Accounts
        </h1>

    </div>


    @if(session('success'))

        <div class="tourist-success">
            {{ session('success') }}
        </div>

    @endif


    <section class="tourist-summary-grid">

        <div class="tourist-summary-card">

            <span class="tourist-summary-label">
                Total Tourists
            </span>

            <strong class="tourist-summary-value">
                {{ number_format($totalTourists) }}
            </strong>

        </div>


        <div class="tourist-summary-card">

            <span class="tourist-summary-label">
                Active
            </span>

            <strong class="tourist-summary-value">
                {{ number_format($activeTourists) }}
            </strong>

        </div>


        <div class="tourist-summary-card">

            <span class="tourist-summary-label">
                Inactive
            </span>

            <strong class="tourist-summary-value">
                {{ number_format($inactiveTourists) }}
            </strong>

        </div>

    </section>


    <section class="tourist-filter-card">

        <form
            method="GET"
            action="{{ route('admin.tourist-accounts.index') }}"
            class="tourist-filter-form"
        >

            <div class="tourist-search">

                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search name, email, or phone"
                >

            </div>


            <div class="tourist-filter-controls">

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


                <select name="period">

                    <option
                        value="all"
                        {{ $period === 'all' ? 'selected' : '' }}
                    >
                        All Time
                    </option>

                    <option
                        value="7"
                        {{ $period === '7' ? 'selected' : '' }}
                    >
                        Last 7 Days
                    </option>

                    <option
                        value="30"
                        {{ $period === '30' ? 'selected' : '' }}
                    >
                        Last 30 Days
                    </option>

                    <option
                        value="90"
                        {{ $period === '90' ? 'selected' : '' }}
                    >
                        Last 90 Days
                    </option>

                </select>


                <button
                    type="submit"
                    class="tourist-filter-button"
                >
                    Filter
                </button>


                @if(
                    $search !== '' ||
                    $status !== 'all' ||
                    $period !== 'all'
                )

                    <a
                        href="{{ route('admin.tourist-accounts.index') }}"
                        class="tourist-clear-button"
                    >
                        Clear
                    </a>

                @endif

            </div>

        </form>

    </section>


    <section class="tourist-table-card">

        <div class="tourist-table-header">

            <div>

                <h2>
                    Tourist Directory
                </h2>

                <span>
                    {{ number_format($tourists->total()) }} accounts
                </span>

            </div>

        </div>


        <div class="tourist-table-wrapper">

            <table class="tourist-table">

                <thead>

                    <tr>
                        <th>Tourist</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>

                </thead>


                <tbody>

                    @forelse($tourists as $tourist)

                        @php

                            $fullName = trim(
                                ($tourist->first_name ?? '') . ' ' .
                                ($tourist->last_name ?? '')
                            );

                            if ($fullName === '') {
                                $fullName = 'Unnamed Tourist';
                            }

                            $initials =
                                strtoupper(
                                    mb_substr(
                                        $tourist->first_name ?? 'T',
                                        0,
                                        1
                                    )
                                    .
                                    mb_substr(
                                        $tourist->last_name ?? '',
                                        0,
                                        1
                                    )
                                );

                        @endphp


                        <tr class="{{ !$tourist->is_active ? 'tourist-row-inactive' : '' }}">

                            <td>

                                <div class="tourist-profile-cell">

                                    <div class="tourist-avatar">

                                        @if(!empty($tourist->profile_image_url))

                                            <img
                                                src="{{ $tourist->profile_image_url }}"
                                                alt="{{ $fullName }}"
                                            >

                                        @else

                                            <span>
                                                {{ $initials }}
                                            </span>

                                        @endif

                                    </div>


                                    <div class="tourist-name">

                                        <strong>
                                            {{ $fullName }}
                                        </strong>

                                        <span>
                                            Tourist
                                        </span>

                                    </div>

                                </div>

                            </td>


                            <td>
                                {{ $tourist->email ?? '—' }}
                            </td>


                            <td>
                                {{ $tourist->phone_number ?? '—' }}
                            </td>


                            <td>

                                @if($tourist->is_active)

                                    <span class="tourist-status tourist-status-active">
                                        Active
                                    </span>

                                @else

                                    <span class="tourist-status tourist-status-inactive">
                                        Inactive
                                    </span>

                                @endif

                            </td>


                            <td>

                                @if(!empty($tourist->created_at))

                                    {{
                                        \Carbon\Carbon::parse(
                                            $tourist->created_at
                                        )->format('M d, Y')
                                    }}

                                @else

                                    —

                                @endif

                            </td>


                            <td>

                                <div class="tourist-actions">

                                    <a
                                        href="{{ route('admin.tourist-accounts.show', $tourist->id) }}"
                                        class="tourist-view-button"
                                    >
                                        View
                                    </a>


                                    @if($tourist->is_active)

                                        <form
                                            method="POST"
                                            action="{{ route('admin.tourist-accounts.status', $tourist->id) }}"
                                            class="tourist-status-form"
                                            onsubmit="return confirm('Deactivate this tourist account? The account data and history will remain stored.');"
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
                                                class="tourist-deactivate-button"
                                            >
                                                Deactivate
                                            </button>

                                        </form>

                                    @else

                                        <form
                                            method="POST"
                                            action="{{ route('admin.tourist-accounts.status', $tourist->id) }}"
                                            class="tourist-status-form"
                                            onsubmit="return confirm('Reactivate this tourist account?');"
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
                                                class="tourist-reactivate-button"
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
                                colspan="6"
                                class="tourist-empty"
                            >
                                No tourist accounts found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <div class="tourist-pagination">

            <div class="tourist-pagination-info">

                Showing

                <strong>
                    {{ $tourists->firstItem() ?? 0 }}
                </strong>

                –

                <strong>
                    {{ $tourists->lastItem() ?? 0 }}
                </strong>

                of

                <strong>
                    {{ number_format($tourists->total()) }}
                </strong>

            </div>


            <div class="tourist-pagination-controls">

                @if($tourists->onFirstPage())

                    <span class="tourist-page-button disabled">
                        Previous
                    </span>

                @else

                    <a
                        href="{{ $tourists->previousPageUrl() }}"
                        class="tourist-page-button"
                    >
                        Previous
                    </a>

                @endif


                <span class="tourist-page-current">

                    {{ $tourists->currentPage() }}
                    /
                    {{ $tourists->lastPage() }}

                </span>


                @if($tourists->hasMorePages())

                    <a
                        href="{{ $tourists->nextPageUrl() }}"
                        class="tourist-page-button"
                    >
                        Next
                    </a>

                @else

                    <span class="tourist-page-button disabled">
                        Next
                    </span>

                @endif

            </div>

        </div>

    </section>

@endsection