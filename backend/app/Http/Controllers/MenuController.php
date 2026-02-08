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
        $query = Menu::with('variants');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $menus = $query->orderBy('menu_category_id', 'desc')->get();

        // Add image_url
        $menus = $menus->map(function ($menu) {
            $menu->image_url = $menu->image
                ? asset('storage/' . $menu->image)
                : null;
            return $menu;
        });

        // ✅ Group by category
        // $grouped = $menus->groupBy('category');

        return response()->json([
            'success' => true,
            'message' => 'Menus retrieved successfully.',
            'data'    => $menus
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

        return response()->json([
            'success' => true,
            'message' => 'Menu deleted successfully.',
        ], 200);
    }
}
