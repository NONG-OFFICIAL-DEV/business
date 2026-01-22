<?php

namespace App\Http\Controllers;

use App\Models\CategoryMenu;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => CategoryMenu::orderBy('id', 'desc')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $categoryMenu = CategoryMenu::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category menu created successfully',
            'data'    => $categoryMenu
        ], Response::HTTP_CREATED);
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
    public function update(Request $request, CategoryMenu $categoryMenu)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:category_menus,slug,' . $categoryMenu->id,
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $categoryMenu->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category menu updated successfully',
            'data'    => $categoryMenu
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoryMenu $categoryMenu)
    {
        $categoryMenu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category menu deleted successfully'
        ]);
    }
}
