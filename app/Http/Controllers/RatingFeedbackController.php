<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatingFeedbackController extends Controller
{
    public function index(Request $request)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $search = trim(
            (string) $request->input('search', '')
        );

        $rating = $request->input(
            'rating',
            'all'
        );

        $spot = $request->input(
            'spot',
            'all'
        );


        $query = DB::table('reviews as r')
            ->leftJoin(
                'profiles as p',
                'p.id',
                '=',
                'r.user_id'
            )
            ->leftJoin(
                'tourist_spots as ts',
                'ts.tourist_id',
                '=',
                'r.tourist_id'
            )
            ->select(
                'r.review_id',
                'r.user_id',
                'r.tourist_id',
                'r.rating',
                'r.comment',
                'r.created_at',
                'p.first_name',
                'p.last_name',
                'ts.name as tourist_spot'
            );


        if ($search !== '') {

            $query->where(function ($subQuery) use ($search) {

                $subQuery
                    ->where(
                        'p.first_name',
                        'ilike',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'p.last_name',
                        'ilike',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'ts.name',
                        'ilike',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'r.comment',
                        'ilike',
                        '%' . $search . '%'
                    );

            });

        }


        if (
            $rating !== 'all' &&
            in_array(
                (int) $rating,
                [1, 2, 3, 4, 5],
                true
            )
        ) {

            $query->where(
                'r.rating',
                (int) $rating
            );

        }


        if (
            $spot !== 'all' &&
            is_numeric($spot)
        ) {

            $query->where(
                'r.tourist_id',
                (int) $spot
            );

        }


        $reviews = $query
            ->orderByDesc('r.created_at')
            ->paginate(8)
            ->withQueryString();


        $summary = DB::table('reviews')
            ->selectRaw('
                COUNT(*) as total_reviews,
                ROUND(
                    AVG(rating)::numeric,
                    2
                ) as average_rating,
                COUNT(*) FILTER (
                    WHERE rating = 5
                ) as five_star_reviews,
                COUNT(
                    DISTINCT tourist_id
                ) as reviewed_spots
            ')
            ->first();


        $touristSpots = DB::table('tourist_spots')
            ->select(
                'tourist_id',
                'name'
            )
            ->orderBy('name')
            ->get();


        return view(
            'admin.ratings-feedback.index',
            [
                'reviews' => $reviews,
                'search' => $search,
                'rating' => $rating,
                'spot' => $spot,
                'touristSpots' => $touristSpots,
                'totalReviews' =>
                    (int) ($summary->total_reviews ?? 0),
                'averageRating' =>
                    $summary->average_rating ?? null,
                'fiveStarReviews' =>
                    (int) ($summary->five_star_reviews ?? 0),
                'reviewedSpots' =>
                    (int) ($summary->reviewed_spots ?? 0),
            ]
        );
    }


    public function show($id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $review = DB::table('reviews as r')
            ->leftJoin(
                'profiles as p',
                'p.id',
                '=',
                'r.user_id'
            )
            ->leftJoin(
                'auth.users as au',
                'au.id',
                '=',
                'r.user_id'
            )
            ->leftJoin(
                'tourist_spots as ts',
                'ts.tourist_id',
                '=',
                'r.tourist_id'
            )
            ->select(
                'r.review_id',
                'r.user_id',
                'r.tourist_id',
                'r.rating',
                'r.comment',
                'r.created_at',
                'p.first_name',
                'p.last_name',
                'p.phone_number',
                'au.email',
                'ts.name as tourist_spot',
                'ts.category as tourist_spot_category',
                'ts.address as tourist_spot_address'
            )
            ->where(
                'r.review_id',
                $id
            )
            ->first();


        abort_if(
            !$review,
            404
        );


        return view(
            'admin.ratings-feedback.show',
            [
                'review' => $review,
            ]
        );
    }
}