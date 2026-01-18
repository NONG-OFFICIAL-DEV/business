<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Menu::query();

        // Filter by status if provided (?status=1 or ?status=0)
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Pagination (default 10 per page)
        $menus = $query->orderBy('id', 'desc')->paginate(10);

        $menus->getCollection()->transform(function ($menu) {
            $menu->image_url = $menu->image
                ? asset('storage/' . $menu->image)
                : null;
            return $menu;
        });

        return response()->json([
            'success'   => true,
            'message'   => 'Suppliers retrieved successfully.',
            'data'      => $menus,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return Menu::store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return Menu::store($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        return response()->json('Deleted successfully', 204);
    }
}
