@extends('admin.layouts.app')

@section('title', 'CityPulse | Review Details')
@section('body-class', 'rating-feedback-show-page')

@push('styles')

    <link
        rel="stylesheet"
        href="{{ asset('css/pages/rating-feedback-show.css') }}"
    >

@endpush


@section('content')

    @php

        $touristName = trim(
            ($review->first_name ?? '') . ' ' .
            ($review->last_name ?? '')
        );

        if ($touristName === '') {
            $touristName = 'Unknown Tourist';
        }

    @endphp


    <div class="review-show-header">

        <div>

            <a
                href="{{ route('admin.ratings-feedback.index') }}"
                class="review-back-link"
            >
                Back to Ratings & Feedback
            </a>

            <h1>
                Review Details
            </h1>

        </div>

    </div>


    <div class="review-show-layout">

        <section class="review-card review-rating-card">

            <span class="review-card-label">
                Rating
            </span>

            <strong class="review-large-rating">
                {{ $review->rating }} / 5
            </strong>

        </section>


        <section class="review-card">

            <h2>
                Tourist
            </h2>

            <div class="review-info-list">

                <div>

                    <span>
                        Name
                    </span>

                    <strong>
                        {{ $touristName }}
                    </strong>

                </div>


                <div>

                    <span>
                        Email
                    </span>

                    <strong>
                        {{ $review->email ?? 'Not available' }}
                    </strong>

                </div>


                <div>

                    <span>
                        Phone
                    </span>

                    <strong>
                        {{ $review->phone_number ?? 'Not available' }}
                    </strong>

                </div>

            </div>

        </section>


        <section class="review-card">

            <h2>
                Tourist Spot
            </h2>

            <div class="review-info-list">

                <div>

                    <span>
                        Destination
                    </span>

                    <strong>
                        {{ $review->tourist_spot ?? 'Unknown Tourist Spot' }}
                    </strong>

                </div>


                <div>

                    <span>
                        Category
                    </span>

                    <strong>
                        {{ $review->tourist_spot_category ?? 'Not available' }}
                    </strong>

                </div>


                <div>

                    <span>
                        Address
                    </span>

                    <strong>
                        {{ $review->tourist_spot_address ?? 'Not available' }}
                    </strong>

                </div>

            </div>

        </section>


        <section class="review-card review-feedback-card">

            <h2>
                Feedback
            </h2>

            <div class="review-feedback-text">

                @if(!empty($review->comment))

                    {{ $review->comment }}

                @else

                    No written feedback was provided.

                @endif

            </div>

        </section>


        <section class="review-card">

            <h2>
                Review Information
            </h2>

            <div class="review-info-list">

                <div>

                    <span>
                        Review ID
                    </span>

                    <strong>
                        #{{ $review->review_id }}
                    </strong>

                </div>


                <div>

                    <span>
                        Submitted
                    </span>

                    <strong>
                        {{
                            \Carbon\Carbon::parse(
                                $review->created_at
                            )
                            ->timezone('Asia/Manila')
                            ->format('M d, Y h:i A')
                        }}
                    </strong>

                </div>

            </div>

        </section>

    </div>

@endsection