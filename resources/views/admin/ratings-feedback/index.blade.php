@extends('admin.layouts.app')

@section('title', 'CityPulse | Ratings & Feedback')
@section('body-class', 'ratings-feedback-page')

@push('styles')

    <link
        rel="stylesheet"
        href="{{ asset('css/pages/ratings-feedback.css') }}"
    >

@endpush


@section('content')

    <div class="feedback-header">

        <h1>
            Ratings & Feedback
        </h1>

    </div>


    <section class="feedback-summary-grid">

        <div class="feedback-summary-card">

            <span>
                Total Reviews
            </span>

            <strong>
                {{ number_format($totalReviews) }}
            </strong>

        </div>


        <div class="feedback-summary-card">

            <span>
                Average Rating
            </span>

            <strong>
                {{ $averageRating !== null
                    ? number_format((float) $averageRating, 2)
                    : '—'
                }}
            </strong>

        </div>


        <div class="feedback-summary-card">

            <span>
                5-Star Reviews
            </span>

            <strong>
                {{ number_format($fiveStarReviews) }}
            </strong>

        </div>


        <div class="feedback-summary-card">

            <span>
                Tourist Spots Reviewed
            </span>

            <strong>
                {{ number_format($reviewedSpots) }}
            </strong>

        </div>

    </section>


    <section class="feedback-filter-card">

        <form
            method="GET"
            action="{{ route('admin.ratings-feedback.index') }}"
            class="feedback-filter-form"
        >

            <input
                type="search"
                name="search"
                value="{{ $search }}"
                placeholder="Search tourist, destination, or feedback"
            >


            <div class="feedback-filter-controls">

                <select name="rating">

                    <option value="all">
                        All Ratings
                    </option>

                    @for($ratingOption = 5; $ratingOption >= 1; $ratingOption--)

                        <option
                            value="{{ $ratingOption }}"
                            {{ (string) $rating === (string) $ratingOption
                                ? 'selected'
                                : ''
                            }}
                        >
                            {{ $ratingOption }} Star{{ $ratingOption > 1 ? 's' : '' }}
                        </option>

                    @endfor

                </select>


                <select name="spot">

                    <option value="all">
                        All Tourist Spots
                    </option>

                    @foreach($touristSpots as $spotOption)

                        <option
                            value="{{ $spotOption->tourist_id }}"
                            {{ (string) $spot === (string) $spotOption->tourist_id
                                ? 'selected'
                                : ''
                            }}
                        >
                            {{ $spotOption->name }}
                        </option>

                    @endforeach

                </select>


                <button
                    type="submit"
                    class="feedback-filter-button"
                >
                    Filter
                </button>


                @if(
                    $search !== '' ||
                    $rating !== 'all' ||
                    $spot !== 'all'
                )

                    <a
                        href="{{ route('admin.ratings-feedback.index') }}"
                        class="feedback-clear-button"
                    >
                        Clear
                    </a>

                @endif

            </div>

        </form>

    </section>


    <section class="feedback-table-card">

        <div class="feedback-table-header">

            <div>

                <h2>
                    Tourist Reviews
                </h2>

                <span>
                    {{ number_format($reviews->total()) }} records
                </span>

            </div>

        </div>


        <div class="feedback-table-wrapper">

            <table class="feedback-table">

                <thead>

                    <tr>
                        <th>Tourist</th>
                        <th>Tourist Spot</th>
                        <th>Rating</th>
                        <th>Feedback</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>

                </thead>


                <tbody>

                    @forelse($reviews as $review)

                        @php

                            $touristName = trim(
                                ($review->first_name ?? '') . ' ' .
                                ($review->last_name ?? '')
                            );

                            if ($touristName === '') {
                                $touristName = 'Unknown Tourist';
                            }

                        @endphp


                        <tr>

                            <td>

                                <div class="feedback-tourist-cell">

                                    <strong>
                                        {{ $touristName }}
                                    </strong>

                                    <span>
                                        Review #{{ $review->review_id }}
                                    </span>

                                </div>

                            </td>


                            <td>

                                <strong class="feedback-spot-name">
                                    {{ $review->tourist_spot ?? 'Unknown Tourist Spot' }}
                                </strong>

                            </td>


                            <td>

                                <span class="feedback-rating-badge">
                                    {{ $review->rating }} / 5
                                </span>

                            </td>


                            <td>

                                <div class="feedback-comment">

                                    @if(!empty($review->comment))

                                        {{ \Illuminate\Support\Str::limit(
                                            $review->comment,
                                            90
                                        ) }}

                                    @else

                                        <span class="feedback-no-comment">
                                            No written feedback
                                        </span>

                                    @endif

                                </div>

                            </td>


                            <td>

                                <div class="feedback-date">

                                    <strong>
                                        {{
                                            \Carbon\Carbon::parse(
                                                $review->created_at
                                            )
                                            ->timezone('Asia/Manila')
                                            ->format('M d, Y')
                                        }}
                                    </strong>

                                    <span>
                                        {{
                                            \Carbon\Carbon::parse(
                                                $review->created_at
                                            )
                                            ->timezone('Asia/Manila')
                                            ->format('h:i A')
                                        }}
                                    </span>

                                </div>

                            </td>


                            <td>

                                <a
                                    href="{{ route(
                                        'admin.ratings-feedback.show',
                                        $review->review_id
                                    ) }}"
                                    class="feedback-view-button"
                                >
                                    View
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="feedback-empty"
                            >

                                <strong>
                                    No ratings or feedback yet.
                                </strong>

                                <span>
                                    Tourist reviews will appear here after they submit ratings from the CityPulse mobile application.
                                </span>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($reviews->total() > 0)

            <div class="feedback-pagination">

                <span>

                    Showing
                    {{ $reviews->firstItem() ?? 0 }}
                    –
                    {{ $reviews->lastItem() ?? 0 }}
                    of
                    {{ $reviews->total() }}

                </span>


                <div>

                    @if($reviews->onFirstPage())

                        <span class="feedback-page-button disabled">
                            Previous
                        </span>

                    @else

                        <a
                            href="{{ $reviews->previousPageUrl() }}"
                            class="feedback-page-button"
                        >
                            Previous
                        </a>

                    @endif


                    <span class="feedback-page-current">

                        {{ $reviews->currentPage() }}
                        /
                        {{ $reviews->lastPage() }}

                    </span>


                    @if($reviews->hasMorePages())

                        <a
                            href="{{ $reviews->nextPageUrl() }}"
                            class="feedback-page-button"
                        >
                            Next
                        </a>

                    @else

                        <span class="feedback-page-button disabled">
                            Next
                        </span>

                    @endif

                </div>

            </div>

        @endif

    </section>

@endsection