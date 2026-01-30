<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
        ]);

        $movements = StockMovement::with('user:id,name')
            ->where('product_id', $request->product_id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($m) {
                return [
                    'user_name'     => $m->user->name ?? 'System',
                    'movement_type' => $m->movement_type,
                    'qty'           => (float) $m->qty,
                    'total_cost'    => round((float) $m->qty * (float) $m->cost, 2),
                    'date'          => $m->created_at,
                ];
            });


        return response()->json([
            'success' => true,
            'message' => 'Stock movements fetched successfully',
            'data' => $movements,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
