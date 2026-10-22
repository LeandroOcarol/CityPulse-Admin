@extends('admin.layouts.app')

@section('title', 'CityPulse | Safety Alerts')
@section('body-class', 'safety-alerts-page')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/pages/safety-alerts.css') }}"
    >
@endpush

@section('content')

    <div class="alerts-header">

        <h1>
            Safety Alerts
        </h1>

        <a
            href="{{ route('admin.safety-alerts.create') }}"
            class="alerts-add-button"
        >
            Add Safety Alert
        </a>

    </div>


    @if(session('success'))

        <div class="alerts-success">
            {{ session('success') }}
        </div>

    @endif


    <section class="alerts-summary-grid">

        <div class="alerts-summary-card">
            <span>Total Alerts</span>
            <strong>{{ number_format($totalAlerts) }}</strong>
        </div>

        <div class="alerts-summary-card">
            <span>Active</span>
            <strong>{{ number_format($activeAlerts) }}</strong>
        </div>

        <div class="alerts-summary-card">
            <span>Inactive</span>
            <strong>{{ number_format($inactiveAlerts) }}</strong>
        </div>

        <div class="alerts-summary-card">
            <span>Emergency</span>
            <strong>{{ number_format($emergencyAlerts) }}</strong>
        </div>

    </section>


    <section class="alerts-filter-card">

        <form
            method="GET"
            action="{{ route('admin.safety-alerts.index') }}"
            class="alerts-filter-form"
        >

            <input
                type="search"
                name="search"
                value="{{ $search }}"
                placeholder="Search alerts or zones"
            >


            <div class="alerts-filter-controls">

                <select name="level">

                    <option value="all">
                        All Levels
                    </option>

                    @foreach(['Low', 'Medium', 'High', 'Emergency'] as $levelOption)

                        <option
                            value="{{ $levelOption }}"
                            {{ $level === $levelOption ? 'selected' : '' }}
                        >
                            {{ $levelOption }}
                        </option>

                    @endforeach

                </select>


                <select name="status">

                    <option value="all">
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


                <select name="zone">

                    <option value="all">
                        All Zones
                    </option>

                    @foreach($zones as $zoneOption)

                        <option
                            value="{{ $zoneOption->zone_id }}"
                            {{ (string) $zone === (string) $zoneOption->zone_id ? 'selected' : '' }}
                        >
                            {{ $zoneOption->zone_name }}
                        </option>

                    @endforeach

                </select>


                <button
                    type="submit"
                    class="alerts-filter-button"
                >
                    Filter
                </button>


                @if(
                    $search !== '' ||
                    $level !== 'all' ||
                    $status !== 'all' ||
                    $zone !== 'all'
                )

                    <a
                        href="{{ route('admin.safety-alerts.index') }}"
                        class="alerts-clear-button"
                    >
                        Clear
                    </a>

                @endif

            </div>

        </form>

    </section>


    <section class="alerts-table-card">

        <div class="alerts-table-header">

            <div>

                <h2>
                    Safety Alert Directory
                </h2>

                <span>
                    {{ number_format($alerts->total()) }} records
                </span>

            </div>

        </div>


        <div class="alerts-table-wrapper">

            <table class="alerts-table">

                <thead>

                    <tr>
                        <th>Alert</th>
                        <th>Zone</th>
                        <th>Level</th>
                        <th>Status</th>
                        <th>Starts</th>
                        <th>Ends</th>
                        <th>Actions</th>
                    </tr>

                </thead>


                <tbody>

                    @forelse($alerts as $alert)

                        @php
                            $levelClass = strtolower(
                                $alert->alert_level
                            );
                        @endphp


                        <tr class="{{ !$alert->is_active ? 'alerts-row-inactive' : '' }}">

                            <td>

                                <div class="alerts-title-cell">

                                    <strong>
                                        {{ $alert->title }}
                                    </strong>

                                    <span>
                                        Alert #{{ $alert->alert_id }}
                                    </span>

                                </div>

                            </td>


                            <td>

                                <div class="alerts-zone-cell">

                                    <strong>
                                        {{ $alert->zone_name ?? 'Unknown Zone' }}
                                    </strong>

                                    @if(!empty($alert->zone_type))

                                        <span>
                                            {{ $alert->zone_type }}
                                        </span>

                                    @endif

                                </div>

                            </td>


                            <td>

                                <span class="alerts-level alerts-level-{{ $levelClass }}">
                                    {{ $alert->alert_level }}
                                </span>

                            </td>


                            <td>

                                @if($alert->is_active)

                                    <span class="alerts-status alerts-status-active">
                                        Active
                                    </span>

                                @else

                                    <span class="alerts-status alerts-status-inactive">
                                        Inactive
                                    </span>

                                @endif

                            </td>


                            <td>

                                <div class="alerts-date-cell">

                                    <strong>
                                        {{
                                            \Carbon\Carbon::parse(
                                                $alert->starts_at
                                            )
                                            ->timezone('Asia/Manila')
                                            ->format('M d, Y')
                                        }}
                                    </strong>

                                    <span>
                                        {{
                                            \Carbon\Carbon::parse(
                                                $alert->starts_at
                                            )
                                            ->timezone('Asia/Manila')
                                            ->format('h:i A')
                                        }}
                                    </span>

                                </div>

                            </td>


                            <td>

                                <div class="alerts-date-cell">

                                    @if(!empty($alert->ends_at))

                                        <strong>
                                            {{
                                                \Carbon\Carbon::parse(
                                                    $alert->ends_at
                                                )
                                                ->timezone('Asia/Manila')
                                                ->format('M d, Y')
                                            }}
                                        </strong>

                                        <span>
                                            {{
                                                \Carbon\Carbon::parse(
                                                    $alert->ends_at
                                                )
                                                ->timezone('Asia/Manila')
                                                ->format('h:i A')
                                            }}
                                        </span>

                                    @else

                                        <strong>
                                            No end date
                                        </strong>

                                    @endif

                                </div>

                            </td>


                            <td>

                                <div class="alerts-actions">

                                    <a
                                        href="{{ route('admin.safety-alerts.show', $alert->alert_id) }}"
                                        class="alerts-view-button"
                                    >
                                        View
                                    </a>


                                    <details class="alerts-more">

                                        <summary>
                                            More
                                        </summary>


                                        <div class="alerts-more-menu">

                                            <a
                                                href="{{ route('admin.safety-alerts.edit', $alert->alert_id) }}"
                                                class="alerts-more-item"
                                            >
                                                Edit
                                            </a>


                                            @if($alert->is_active)

                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.safety-alerts.status', $alert->alert_id) }}"
                                                    onsubmit="return confirm('Deactivate this safety alert?');"
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
                                                        class="alerts-more-item"
                                                    >
                                                        Deactivate
                                                    </button>

                                                </form>

                                            @else

                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.safety-alerts.status', $alert->alert_id) }}"
                                                    onsubmit="return confirm('Reactivate this safety alert?');"
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
                                                        class="alerts-more-item"
                                                    >
                                                        Reactivate
                                                    </button>

                                                </form>

                                            @endif


                                            <div class="alerts-more-divider"></div>


                                            <form
                                                method="POST"
                                                action="{{ route('admin.safety-alerts.destroy', $alert->alert_id) }}"
                                                onsubmit="return confirm('Are you sure you want to permanently delete this safety alert?');"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="alerts-more-item alerts-more-delete"
                                                >
                                                    Delete
                                                </button>

                                            </form>

                                        </div>

                                    </details>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="alerts-empty"
                            >
                                No safety alerts found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <div class="alerts-pagination">

            <span>

                Showing
                {{ $alerts->firstItem() ?? 0 }}
                –
                {{ $alerts->lastItem() ?? 0 }}
                of
                {{ $alerts->total() }}

            </span>


            <div>

                @if($alerts->onFirstPage())

                    <span class="alerts-page-button disabled">
                        Previous
                    </span>

                @else

                    <a
                        href="{{ $alerts->previousPageUrl() }}"
                        class="alerts-page-button"
                    >
                        Previous
                    </a>

                @endif


                <span class="alerts-page-current">
                    {{ $alerts->currentPage() }}
                    /
                    {{ $alerts->lastPage() }}
                </span>


                @if($alerts->hasMorePages())

                    <a
                        href="{{ $alerts->nextPageUrl() }}"
                        class="alerts-page-button"
                    >
                        Next
                    </a>

                @else

                    <span class="alerts-page-button disabled">
                        Next
                    </span>

                @endif

            </div>

        </div>

    </section>

@endsection