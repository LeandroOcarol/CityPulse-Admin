<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TouristSpotController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | TOURIST SPOT DIRECTORY
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $search = trim($request->get('search', ''));
        $category = trim($request->get('category', 'all'));

        $query = DB::table('tourist_spots')
            ->select(
                'tourist_id',
                'name',
                'description',
                'history',
                'latitude',
                'longitude',
                'image',
                'category',
                'created_at'
            );


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where(
                    'name',
                    'ilike',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'category',
                    'ilike',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'description',
                    'ilike',
                    '%' . $search . '%'
                );
            });
        }


        /*
        |--------------------------------------------------------------------------
        | CATEGORY FILTER
        |--------------------------------------------------------------------------
        */

        if ($category !== 'all') {
            $query->where(
                'category',
                $category
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $touristSpots = $query
            ->orderByDesc('created_at')
            ->paginate(8)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | SUMMARY DATA
        |--------------------------------------------------------------------------
        */

        $totalSpots = DB::table('tourist_spots')
            ->count();

        $totalCategories = DB::table('tourist_spots')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->count('category');

        $categories = DB::table('tourist_spots')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');


        return view(
            'admin.tourist-spots.index',
            [
                'touristSpots' => $touristSpots,
                'totalSpots' => $totalSpots,
                'totalCategories' => $totalCategories,
                'categories' => $categories,
                'search' => $search,
                'category' => $category,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ADD TOURIST SPOT PAGE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $categories = DB::table('tourist_spots')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view(
            'admin.tourist-spots.create',
            [
                'categories' => $categories,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SAVE TOURIST SPOT
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'category' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'description' => [
                    'required',
                    'string',
                ],

                'history' => [
                    'nullable',
                    'string',
                ],

                'latitude' => [
                    'required',
                    'numeric',
                    'between:-90,90',
                ],

                'longitude' => [
                    'required',
                    'numeric',
                    'between:-180,180',
                ],

                'image' => [
                    'nullable',
                    'url',
                    'max:2000',
                ],
            ],
            [
                'name.required' => 'Spot name is required.',
                'category.required' => 'Category is required.',
                'description.required' => 'Description is required.',
                'latitude.required' => 'Latitude is required.',
                'longitude.required' => 'Longitude is required.',
                'latitude.between' => 'Enter a valid latitude.',
                'longitude.between' => 'Enter a valid longitude.',
                'image.url' => 'Enter a valid image URL.',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | INSERT INTO SUPABASE
        |--------------------------------------------------------------------------
        |
        | Laravel is already connected directly to the same Supabase PostgreSQL
        | database used by the CityPulse Android application.
        |
        */

        DB::table('tourist_spots')->insert(
            [
                'name' => trim($validated['name']),
                'category' => trim($validated['category']),
                'description' => trim($validated['description']),

                'history' => !empty($validated['history'])
                    ? trim($validated['history'])
                    : null,

                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],

                'image' => !empty($validated['image'])
                    ? trim($validated['image'])
                    : null,

                'created_at' => now(),
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | RETURN TO DIRECTORY
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('admin.tourist-spots.index')
            ->with(
                'success',
                'Tourist spot added.'
            );
    }
}