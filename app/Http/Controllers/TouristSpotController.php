<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class TouristSpotController extends Controller
{
    public function index(Request $request)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $search = trim(
            (string) $request->input('search', '')
        );

        $category = $request->input(
            'category',
            'all'
        );

        $status = $request->input(
            'status',
            'all'
        );

        $query = DB::table('tourist_spots')
            ->select(
                'tourist_id',
                'name',
                'description',
                'history',
                'image_url',
                'category',
                'address',
                'opening_hours',
                'created_at',
                'is_active'
            )
            ->selectRaw(
                'extensions.ST_Y(
                    location::extensions.geometry
                ) AS latitude'
            )
            ->selectRaw(
                'extensions.ST_X(
                    location::extensions.geometry
                ) AS longitude'
            );

        if ($search !== '') {

            $query->where(function ($subQuery) use ($search) {

                $subQuery
                    ->where(
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
                        'address',
                        'ilike',
                        '%' . $search . '%'
                    );

            });

        }

        if ($category !== 'all') {

            $query->where(
                'category',
                $category
            );

        }

        if ($status === 'active') {

            $query->where(
                'is_active',
                true
            );

        } elseif ($status === 'inactive') {

            $query->where(
                'is_active',
                false
            );

        }

        $touristSpots = $query
            ->orderByDesc('is_active')
            ->orderByDesc('created_at')
            ->paginate(8)
            ->withQueryString();

        $totalSpots = DB::table('tourist_spots')
            ->count();

        $activeSpots = DB::table('tourist_spots')
            ->where(
                'is_active',
                true
            )
            ->count();

        $inactiveSpots = DB::table('tourist_spots')
            ->where(
                'is_active',
                false
            )
            ->count();

        $totalCategories = DB::table('tourist_spots')
            ->whereNotNull('category')
            ->where(
                'category',
                '!=',
                ''
            )
            ->distinct()
            ->count('category');

        $categories = DB::table('tourist_spots')
            ->whereNotNull('category')
            ->where(
                'category',
                '!=',
                ''
            )
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view(
            'admin.tourist-spots.index',
            [
                'touristSpots' => $touristSpots,

                // Kept for compatibility with any older references.
                'spots' => $touristSpots,

                'search' => $search,
                'category' => $category,
                'status' => $status,
                'categories' => $categories,

                'totalSpots' => $totalSpots,
                'activeSpots' => $activeSpots,
                'inactiveSpots' => $inactiveSpots,

                'totalCategories' => $totalCategories,

                // Kept for compatibility with older references.
                'categoryCount' => $totalCategories,
            ]
        );
    }


    public function create()
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $categories = DB::table('tourist_spots')
            ->whereNotNull('category')
            ->where(
                'category',
                '!=',
                ''
            )
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


    public function store(Request $request)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'history' => [
                'nullable',
                'string',
            ],

            'image_url' => [
                'nullable',
                'url',
                'max:2048',
            ],

            'spot_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],

            'category' => [
                'required',
                'string',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
                'max:500',
            ],

            'opening_hours' => [
                'nullable',
                'string',
                'max:255',
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
        ]);

        try {

            $imageUrl = $this->resolveImageUrl(
                $request,
                $validated['image_url'] ?? null,
                null,
                $validated['name']
            );

        } catch (RuntimeException $exception) {

            return back()
                ->withInput()
                ->withErrors([
                    'spot_photo' =>
                        $exception->getMessage(),
                ]);

        }

        DB::table('tourist_spots')
            ->insert([
                'name' =>
                    $validated['name'],

                'description' =>
                    $validated['description'],

                'history' =>
                    $validated['history'] ?? null,

                'image_url' =>
                    $imageUrl,

                'category' =>
                    $validated['category'],

                'address' =>
                    $validated['address'] ?? null,

                'opening_hours' =>
                    $validated['opening_hours'] ?? null,

                'location' => DB::raw(
                    "extensions.ST_SetSRID(
                        extensions.ST_MakePoint(
                            {$validated['longitude']},
                            {$validated['latitude']}
                        ),
                        4326
                    )::extensions.geography"
                ),

                'is_active' =>
                    true,

                'created_at' =>
                    now(),
            ]);

        return redirect()
            ->route(
                'admin.tourist-spots.index'
            )
            ->with(
                'success',
                'Tourist spot added successfully.'
            );
    }


    public function show($id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $spot = $this->getSpot(
            $id
        );

        abort_if(
            !$spot,
            404
        );

        return view(
            'admin.tourist-spots.show',
            [
                'spot' => $spot,
            ]
        );
    }


    public function edit($id)
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $spot = $this->getSpot(
            $id
        );

        abort_if(
            !$spot,
            404
        );

        $categories = DB::table('tourist_spots')
            ->whereNotNull('category')
            ->where(
                'category',
                '!=',
                ''
            )
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view(
            'admin.tourist-spots.edit',
            [
                'spot' => $spot,
                'categories' => $categories,
            ]
        );
    }


    public function update(
        Request $request,
        $id
    ) {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $spot = $this->getSpot(
            $id
        );

        abort_if(
            !$spot,
            404
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'history' => [
                'nullable',
                'string',
            ],

            'image_url' => [
                'nullable',
                'url',
                'max:2048',
            ],

            'spot_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],

            'category' => [
                'required',
                'string',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
                'max:500',
            ],

            'opening_hours' => [
                'nullable',
                'string',
                'max:255',
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
        ]);

        try {

            $imageUrl = $this->resolveImageUrl(
                $request,
                $validated['image_url'] ?? null,
                $spot->image_url,
                $validated['name']
            );

        } catch (RuntimeException $exception) {

            return back()
                ->withInput()
                ->withErrors([
                    'spot_photo' =>
                        $exception->getMessage(),
                ]);

        }

        DB::table('tourist_spots')
            ->where(
                'tourist_id',
                $id
            )
            ->update([
                'name' =>
                    $validated['name'],

                'description' =>
                    $validated['description'],

                'history' =>
                    $validated['history'] ?? null,

                'image_url' =>
                    $imageUrl,

                'category' =>
                    $validated['category'],

                'address' =>
                    $validated['address'] ?? null,

                'opening_hours' =>
                    $validated['opening_hours'] ?? null,

                'location' => DB::raw(
                    "extensions.ST_SetSRID(
                        extensions.ST_MakePoint(
                            {$validated['longitude']},
                            {$validated['latitude']}
                        ),
                        4326
                    )::extensions.geography"
                ),
            ]);

        return redirect()
            ->route(
                'admin.tourist-spots.show',
                $id
            )
            ->with(
                'success',
                'Tourist spot updated successfully.'
            );
    }


    public function updateStatus(
        Request $request,
        $id
    ) {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $validated = $request->validate([
            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $spotExists = DB::table('tourist_spots')
            ->where(
                'tourist_id',
                $id
            )
            ->exists();

        abort_if(
            !$spotExists,
            404
        );

        DB::table('tourist_spots')
            ->where(
                'tourist_id',
                $id
            )
            ->update([
                'is_active' =>
                    (bool) $validated['is_active'],
            ]);

        $message =
            (bool) $validated['is_active']
                ? 'Tourist spot reactivated successfully.'
                : 'Tourist spot deactivated successfully.';

        return back()
            ->with(
                'success',
                $message
            );
    }


    private function resolveImageUrl(
        Request $request,
        ?string $enteredUrl,
        ?string $existingUrl,
        string $spotName
    ): ?string {
        if (
            $request->hasFile(
                'spot_photo'
            )
        ) {

            return $this->uploadSpotPhoto(
                $request->file(
                    'spot_photo'
                ),
                $spotName
            );

        }

        if (
            $enteredUrl !== null &&
            trim($enteredUrl) !== ''
        ) {

            return trim(
                $enteredUrl
            );

        }

        return $existingUrl;
    }


    private function uploadSpotPhoto(
        UploadedFile $photo,
        string $spotName
    ): string {
        $supabaseUrl = rtrim(
            (string) env(
                'SUPABASE_URL'
            ),
            '/'
        );

        $secretKey = trim(
            (string) env(
                'SUPABASE_SECRET_KEY'
            )
        );

        $bucket = trim(
            (string) env(
                'SUPABASE_TOURIST_SPOT_BUCKET',
                'tourist-spots'
            )
        );

        if ($supabaseUrl === '') {

            throw new RuntimeException(
                'SUPABASE_URL is not configured.'
            );

        }

        if ($secretKey === '') {

            throw new RuntimeException(
                'SUPABASE_SECRET_KEY is not configured.'
            );

        }

        if ($bucket === '') {

            throw new RuntimeException(
                'The tourist spot Storage bucket is not configured.'
            );

        }

        $extension = strtolower(
            $photo->getClientOriginalExtension()
        );

        if ($extension === '') {

            $extension = match (
                $photo->getMimeType()
            ) {
                'image/png' =>
                    'png',

                'image/webp' =>
                    'webp',

                default =>
                    'jpg',
            };

        }

        $safeSpotName = Str::slug(
            $spotName
        );

        if ($safeSpotName === '') {

            $safeSpotName =
                'tourist-spot';

        }

        $fileName =
            $safeSpotName
            . '-'
            . Str::uuid()
            . '.'
            . $extension;

        $storagePath =
            'main-images/'
            . $fileName;

        $uploadUrl =
            $supabaseUrl
            . '/storage/v1/object/'
            . rawurlencode($bucket)
            . '/'
            . $this->encodeStoragePath(
                $storagePath
            );

        $fileContents = file_get_contents(
            $photo->getRealPath()
        );

        if ($fileContents === false) {

            throw new RuntimeException(
                'The selected photo could not be read.'
            );

        }

        $mimeType =
            $photo->getMimeType()
            ?: 'application/octet-stream';

        $response = Http::withHeaders([
            'apikey' =>
                $secretKey,

            'Authorization' =>
                'Bearer ' . $secretKey,

            'Content-Type' =>
                $mimeType,

            'x-upsert' =>
                'false',
        ])
            ->withBody(
                $fileContents,
                $mimeType
            )
            ->post(
                $uploadUrl
            );

        if (!$response->successful()) {

            throw new RuntimeException(
                'The photo could not be uploaded to Supabase Storage.'
            );

        }

        return
            $supabaseUrl
            . '/storage/v1/object/public/'
            . rawurlencode($bucket)
            . '/'
            . $this->encodeStoragePath(
                $storagePath
            );
    }


    private function encodeStoragePath(
        string $path
    ): string {
        return collect(
            explode(
                '/',
                $path
            )
        )
            ->map(
                fn ($segment) =>
                    rawurlencode($segment)
            )
            ->implode('/');
    }


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
                'address',
                'opening_hours',
                'created_at',
                'is_active'
            )
            ->selectRaw(
                'extensions.ST_Y(
                    location::extensions.geometry
                ) AS latitude'
            )
            ->selectRaw(
                'extensions.ST_X(
                    location::extensions.geometry
                ) AS longitude'
            )
            ->where(
                'tourist_id',
                $id
            )
            ->first();
    }
}