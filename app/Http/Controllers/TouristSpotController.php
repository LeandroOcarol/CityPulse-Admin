<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TouristSpotController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Tourist Spot Directory
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
                'image_url',
                'category',
                'created_at',
                'address',
                'opening_hours'
            )
            ->selectRaw(
                'CASE
                    WHEN location IS NOT NULL
                    THEN extensions.ST_Y(location::extensions.geometry)
                    ELSE NULL
                END AS latitude'
            )
            ->selectRaw(
                'CASE
                    WHEN location IS NOT NULL
                    THEN extensions.ST_X(location::extensions.geometry)
                    ELSE NULL
                END AS longitude'
            );

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', '%' . $search . '%')
                    ->orWhere('category', 'ilike', '%' . $search . '%')
                    ->orWhere('address', 'ilike', '%' . $search . '%')
                    ->orWhere('description', 'ilike', '%' . $search . '%');
            });
        }

        if ($category !== 'all') {
            $query->where('category', $category);
        }

        $touristSpots = $query
            ->orderByDesc('created_at')
            ->paginate(8)
            ->withQueryString();

        $totalSpots = DB::table('tourist_spots')->count();

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
    | Add Tourist Spot
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $categories = $this->getCategories();

        return view(
            'admin.tourist-spots.create',
            [
                'categories' => $categories,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store Tourist Spot
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $validated = $this->validateSpot($request);

        $latitude = (float) $validated['latitude'];
        $longitude = (float) $validated['longitude'];

        DB::statement(
            '
            INSERT INTO tourist_spots
            (
                name,
                category,
                description,
                history,
                location,
                address,
                opening_hours,
                image_url,
                created_at
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                extensions.ST_SetSRID(
                    extensions.ST_MakePoint(?, ?),
                    4326
                )::extensions.geography,
                ?,
                ?,
                ?,
                ?
            )
            ',
            [
                trim($validated['name']),
                trim($validated['category']),
                trim($validated['description']),
                $this->nullableText($validated['history'] ?? null),
                $longitude,
                $latitude,
                $this->nullableText($validated['address'] ?? null),
                $this->nullableText($validated['opening_hours'] ?? null),
                $this->nullableText($validated['image_url'] ?? null),
                now(),
            ]
        );

        return redirect()
            ->route('admin.tourist-spots.index')
            ->with('success', 'Tourist spot added.');
    }


    /*
    |--------------------------------------------------------------------------
    | Tourist Spot Details
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $spot = $this->getSpot($id);

        abort_if(!$spot, 404);

        return view(
            'admin.tourist-spots.show',
            [
                'spot' => $spot,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Tourist Spot
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $spot = $this->getSpot($id);

        abort_if(!$spot, 404);

        $categories = $this->getCategories();

        return view(
            'admin.tourist-spots.edit',
            [
                'spot' => $spot,
                'categories' => $categories,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Tourist Spot
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $existingSpot = DB::table('tourist_spots')
            ->where('tourist_id', $id)
            ->first();

        abort_if(!$existingSpot, 404);

        $validated = $this->validateSpot($request);

        $latitude = (float) $validated['latitude'];
        $longitude = (float) $validated['longitude'];

        DB::statement(
            '
            UPDATE tourist_spots
            SET
                name = ?,
                category = ?,
                description = ?,
                history = ?,
                location = extensions.ST_SetSRID(
                    extensions.ST_MakePoint(?, ?),
                    4326
                )::extensions.geography,
                address = ?,
                opening_hours = ?,
                image_url = ?
            WHERE tourist_id = ?
            ',
            [
                trim($validated['name']),
                trim($validated['category']),
                trim($validated['description']),
                $this->nullableText($validated['history'] ?? null),
                $longitude,
                $latitude,
                $this->nullableText($validated['address'] ?? null),
                $this->nullableText($validated['opening_hours'] ?? null),
                $this->nullableText($validated['image_url'] ?? null),
                $id,
            ]
        );

        return redirect()
            ->route('admin.tourist-spots.show', $id)
            ->with('success', 'Tourist spot updated.');
    }


    /*
    |--------------------------------------------------------------------------
    | Shared Queries
    |--------------------------------------------------------------------------
    */

    private function getSpot($id)
    {
        return DB::table('tourist_spots')
            ->select(
                'tourist_id',
                'name',
                'description',
                'history',
                'image_url',
                'category',
                'created_at',
                'address',
                'opening_hours'
            )
            ->selectRaw(
                'CASE
                    WHEN location IS NOT NULL
                    THEN extensions.ST_Y(location::extensions.geometry)
                    ELSE NULL
                END AS latitude'
            )
            ->selectRaw(
                'CASE
                    WHEN location IS NOT NULL
                    THEN extensions.ST_X(location::extensions.geometry)
                    ELSE NULL
                END AS longitude'
            )
            ->where('tourist_id', $id)
            ->first();
    }

    private function getCategories()
    {
        return DB::table('tourist_spots')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    private function validateSpot(Request $request)
    {
        return $request->validate(
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

                'address' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'opening_hours' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'image_url' => [
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
                'image_url.url' => 'Enter a valid image URL.',
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function nullableText($value)
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}