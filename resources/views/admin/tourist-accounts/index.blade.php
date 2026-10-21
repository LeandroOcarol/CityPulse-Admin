@extends('admin.layouts.app')

@section('title', 'CityPulse | Tourist Accounts')

@section('body-class', 'tourist-accounts-page')


@push('styles')

    <link
        rel="stylesheet"
        href="/css/pages/tourist-accounts.css"
    >

@endpush


@section('content')


    <!-- Page Header -->

    <div class="tourist-accounts-header">

        <h1>
            Tourist Accounts
        </h1>


        <button
            type="button"
            class="export-button"
        >
            Export Data
        </button>

    </div>



    <!-- Summary -->

    <section class="tourist-account-summary">

        <div class="tourist-total-card">

            <div class="tourist-total-label">
                Total Tourists
            </div>

            <div class="tourist-total-number">
                {{ number_format($totalTourists ?? 0) }}
            </div>

        </div>

    </section>



    <!-- Filters -->

    <section class="tourist-account-filter-panel">

        <form
            method="GET"
            action="{{ route('admin.tourist-accounts.index') }}"
            class="tourist-account-filter-form"
        >

            <div class="account-search-field">

                <input
                    type="search"
                    name="search"
                    value="{{ $search ?? '' }}"
                    placeholder="Search accounts"
                >

            </div>


            <div class="account-period-field">

                <select name="period">

                    <option
                        value="all"
                        {{ ($period ?? 'all') === 'all' ? 'selected' : '' }}
                    >
                        All Dates
                    </option>

                    <option
                        value="7"
                        {{ ($period ?? '') === '7' ? 'selected' : '' }}
                    >
                        Last 7 Days
                    </option>

                    <option
                        value="30"
                        {{ ($period ?? '') === '30' ? 'selected' : '' }}
                    >
                        Last 30 Days
                    </option>

                    <option
                        value="90"
                        {{ ($period ?? '') === '90' ? 'selected' : '' }}
                    >
                        Last 90 Days
                    </option>

                </select>

            </div>


            <button
                type="submit"
                class="account-filter-button"
            >
                Filter
            </button>


            @if(
                ($search ?? '') !== '' ||
                ($period ?? 'all') !== 'all'
            )

                <a
                    href="{{ route('admin.tourist-accounts.index') }}"
                    class="account-clear-button"
                >
                    Clear
                </a>

            @endif

        </form>

    </section>



    <!-- Account Directory -->

    <section class="tourist-account-panel">

        <div class="tourist-account-panel-header">

            <div>

                <h2>
                    Account Directory
                </h2>

                <p>
                    {{ $tourists->total() }} accounts
                </p>

            </div>

        </div>


        @if($tourists->count() > 0)

            <div class="tourist-account-table-wrapper">

                <table class="tourist-account-table">

                    <thead>

                        <tr>
                            <th>Tourist</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Registered</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>

                    </thead>


                    <tbody>

                        @foreach($tourists as $tourist)

                            <tr>

                                <td>

                                    <div class="tourist-profile">

                                        <div class="tourist-avatar">

                                            @if(!empty($tourist->profile_image_url))

                                                <img
                                                    src="{{ $tourist->profile_image_url }}"
                                                    alt=""
                                                >

                                            @else

                                                {{
                                                    strtoupper(
                                                        substr(
                                                            $tourist->first_name ?? 'T',
                                                            0,
                                                            1
                                                        )
                                                    )
                                                }}

                                            @endif

                                        </div>


                                        <div class="tourist-name">

                                            {{ $tourist->first_name ?? '' }}
                                            {{ $tourist->last_name ?? '' }}

                                        </div>

                                    </div>

                                </td>


                                <td>

                                    <span class="account-email">
                                        {{ $tourist->email ?? '—' }}
                                    </span>

                                </td>


                                <td>
                                    {{ $tourist->phone_number ?? '—' }}
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

                                    <span class="tourist-role-badge">
                                        Tourist
                                    </span>

                                </td>


                                <td>

                                    <a
                                        href="{{ route('admin.tourist-accounts.show', $tourist->id) }}"
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



            <!-- Pagination -->

            <div class="tourist-account-pagination">

                <div class="pagination-info">

                    Showing

                    <strong>
                        {{ $tourists->firstItem() }}
                    </strong>

                    –

                    <strong>
                        {{ $tourists->lastItem() }}
                    </strong>

                    of

                    <strong>
                        {{ $tourists->total() }}
                    </strong>

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

                        {{ $tourists->currentPage() }}
                        /
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

            <div class="tourist-account-empty">

                <h3>
                    No accounts found
                </h3>

            </div>

        @endif

    </section>



    <!-- Supporting Information -->

    <section class="tourist-support-grid">

        <div class="support-card">

            <h3>
                Account Logs
            </h3>

            <p>
                Registration and account activity.
            </p>

        </div>


        <div class="support-card">

            <h3>
                Security Policy
            </h3>

            <p>
                Accounts use Supabase authentication.
            </p>

        </div>


        <div class="support-card">

            <h3>
                Data Management
            </h3>

            <p>
                Tourist profiles sync with the mobile app.
            </p>

        </div>

    </section>

@endsection