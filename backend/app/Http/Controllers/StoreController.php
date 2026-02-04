<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;

class StoreController extends Controller
{
    /**
     * GET /api/stores
     */
    public function index(Request $request)
    {
        $query = Store::query();

        // Optional search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Optional filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $stores = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $stores
        ]);
    }

    /**
     * POST /api/stores
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],

            'phone' => ['nullable', 'string', 'max:30'],

            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],

            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],

            'logo' => ['nullable','max:255'],

            'status' => ['nullable', Rule::in(['active', 'inactive'])],

            'opening_time' => ['nullable'],
            'closing_time' => ['nullable'],

            'place_id' => ['nullable', 'string', 'max:120'],
            'formatted_address' => ['nullable', 'string'],

            'google_map_url' => ['nullable', 'string'],
            'delivery_radius_km' => ['nullable', 'numeric'],
        ]);
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('stores', 'public');
            $validated['logo'] = $path; // ex: stores/logo.png
        }
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('stores', 'public');
            $validated['logo'] = $path;
        }

        $validated['code'] = $this->generateStoreCode();

        $validated['created_by'] = JWTAuth::user()->id;

        $store = Store::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Store created successfully',
            'data' => $store
        ], 201);
    }

    /**
     * GET /api/stores/{id}
     */
    public function show(Store $store)
    {
        return response()->json([
            'success' => true,
            'data' => $store
        ]);
    }

    /**
     * PUT /api/stores/{id}
     */
    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],

            'phone' => ['nullable', 'string', 'max:30'],

            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],

            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],

            'logo' => ['nullable', 'max:255'],

            'status' => ['nullable', Rule::in(['active', 'inactive'])],

            'opening_time' => ['nullable'],
            'closing_time' => ['nullable'],

            'place_id' => ['nullable', 'string', 'max:120'],
            'formatted_address' => ['nullable', 'string'],

            'google_map_url' => ['nullable', 'string'],
            'delivery_radius_km' => ['nullable', 'numeric'],
        ]);
        $validated['code'] = $this->generateStoreCode();
        $validated['updated_by'] = JWTAuth::user()->id;

        $store->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Store updated successfully',
            'data' => $store
        ]);
    }

    /**
     * DELETE /api/stores/{id}
     */
    public function destroy(Store $store)
    {
        $store->delete();

        return response()->json([
            'success' => true,
            'message' => 'Store deleted successfully'
        ]);
    }

    private function generateStoreCode(): string
    {
        $lastId = Store::withTrashed()->max('id') ?? 0;
        $nextId = $lastId + 1;

        return 'S' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
