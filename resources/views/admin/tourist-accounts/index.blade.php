@extends('admin.layouts.app')

@section('title', 'CityPulse | Tourist Accounts')

@section('body-class', 'tourist-accounts-page')

@push('styles')
    <link rel="stylesheet" href="/css/pages/tourist-accounts.css">
@endpush

@section('content')

    <div class="accounts-page-header">

        <div>

            <h1>
                Tourist Accounts
            </h1>

            <p>
                Manage and review registered tourist accounts.
            </p>

        </div>


        <button
            type="button"
            class="export-button"
        >
            Export Data
        </button>

    </div>


    <section class="accounts-toolbar">

        <form
            method="GET"
            action="{{ route('admin.tourist-accounts.index') }}"
            class="accounts-filter-form"
        >

            <div class="account-search">

                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search tourist accounts..."
                >

            </div>


            <div class="account-filter">

                <select name="period">

                    <option
                        value="all"
                        {{ $period === 'all' ? 'selected' : '' }}
                    >
                        All Dates
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

            </div>


            <button
                type="submit"
                class="apply-filter-button"
            >
                Apply Filters
            </button>


            @if($search !== '' || $period !== 'all')

                <a
                    href="{{ route('admin.tourist-accounts.index') }}"
                    class="clear-filter-button"
                >
                    Clear
                </a>

            @endif

        </form>


        <div class="total-tourists-card">

            <div class="total-tourists-label">
                Total Tourists
            </div>

            <div class="total-tourists-number">
                {{ number_format($totalTourists) }}
            </div>

        </div>

    </section>


    <section class="accounts-panel">

        <div class="accounts-panel-header">

            <div>

                <h2>
                    Account Directory
                </h2>

                <p>
                    {{ $tourists->total() }}
                    tourist account{{ $tourists->total() === 1 ? '' : 's' }}
                </p>

            </div>

        </div>


        @if($tourists->count() > 0)

            <div class="accounts-table-wrapper">

                <table class="accounts-table">

                    <thead>

                        <tr>
                            <th>Tourist</th>
                            <th>Email</th>
                            <th>Contact Number</th>
                            <th>Account Type</th>
                            <th>Date Registered</th>
                            <th>Action</th>
                        </tr>

                    </thead>


                    <tbody>

                        @foreach($tourists as $tourist)

                            <tr>

                                <td>

                                    <div class="account-user">

                                        <div class="account-avatar">

                                            @if(!empty($tourist->profile_image_url))

                                                <img
                                                    src="{{ $tourist->profile_image_url }}"
                                                    alt=""
                                                >

                                            @else

                                                <span>

                                                    {{
                                                        strtoupper(
                                                            substr(
                                                                $tourist->first_name ?? 'T',
                                                                0,
                                                                1
                                                            )
                                                        )
                                                    }}

                                                </span>

                                            @endif

                                        </div>


                                        <div class="account-user-info">

                                            <div class="account-name">

                                                {{ $tourist->first_name ?? 'Tourist' }}
                                                {{ $tourist->last_name ?? '' }}

                                            </div>

                                            <div class="account-id">
                                                Tourist Account
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <span class="account-email">

                                        {{ $tourist->email ?? 'No email available' }}

                                    </span>

                                </td>


                                <td>
                                    {{ $tourist->phone_number ?? 'Not provided' }}
                                </td>


                                <td>

                                    <span class="tourist-role-badge">
                                        Tourist
                                    </span>

                                </td>


                                <td>

                                    @if($tourist->created_at)

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

                                    <a
                                        href="{{ route(
                                            'admin.tourist-accounts.show',
                                            $tourist->id
                                        ) }}"
                                        class="view-account-button"
                                    >
                                        View
                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            <div class="accounts-pagination">

                <div class="pagination-information">

                    Showing

                    <strong>
                        {{ $tourists->firstItem() }}
                    </strong>

                    to

                    <strong>
                        {{ $tourists->lastItem() }}
                    </strong>

                    of

                    <strong>
                        {{ $tourists->total() }}
                    </strong>

                    accounts

                </div>


                <div class="pagination-controls">

                    @if($tourists->onFirstPage())

                        <span class="pagination-button disabled">
                            Previous
                        </span>

                    @else

                        <a
                            href="{{ $tourists->previousPageUrl() }}"
                            class="pagination-button"
                        >
                            Previous
                        </a>

                    @endif


                    <span class="pagination-current">

                        Page
                        {{ $tourists->currentPage() }}
                        of
                        {{ $tourists->lastPage() }}

                    </span>


                    @if($tourists->hasMorePages())

                        <a
                            href="{{ $tourists->nextPageUrl() }}"
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

            <div class="accounts-empty-state">

                <h3>
                    No tourist accounts found
                </h3>

                <p>
                    Try changing your search or filters.
                </p>

            </div>

        @endif

    </section>


    <section class="account-information-grid">

        <div class="account-information-card">

            <div class="information-title">
                Account Logs
            </div>

            <p>
                Review tourist registration records and account information.
            </p>

        </div>


        <div class="account-information-card">

            <div class="information-title">
                Security Policy
            </div>

            <p>
                Tourist account information is available only to authorized administrators.
            </p>

        </div>


        <div class="account-information-card">

            <div class="information-title">
                Data Management
            </div>

            <p>
                CityPulse uses the same Supabase account data used by the tourist application.
            </p>

        </div>

    </section>

@endsection