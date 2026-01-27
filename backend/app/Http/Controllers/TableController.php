<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tables = Table::query()
            ->when(
                $request->store_id,
                fn($q) =>
                $q->where('store_id', $request->store_id)
            )
            ->orderBy('table_number')
            ->get();

        return response()->json([
            'data' => $tables
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'table_number' => 'required|string|max:50',
            'capacity' => 'nullable|integer|min:1',
            'store_id' => 'nullable|exists:stores,id',
            'area' => 'nullable|string|max:100',
            'note' => 'nullable|string',
            'floor' => 'nullable|integer',
            'position' => 'nullable|integer'
        ]);

        $data['status'] = 'available';
        $data['is_active'] = true;
        $data['qr_token'] = Str::random(10);

        $table = Table::create($data);

        return response()->json([
            'message' => 'Table created successfully',
            'data' => $table
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Table $table)
    {
        return response()->json([
            'data' => $table->load(['store', 'currentOrder'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Table $table)
    {
        $data = $request->validate([
            'table_number' => 'sometimes|string|max:50',
            'capacity' => 'nullable|integer|min:1',
            'status' => 'in:available,occupied,reserved,disabled',
            'area' => 'nullable|string|max:100',
            'note' => 'nullable|string',
            'is_active' => 'boolean',
            'floor' => 'nullable|integer',
            'position' => 'nullable|integer',
            'assigned_staff_id' => 'nullable|exists:users,id'
        ]);

        $table->update($data);

        return response()->json([
            'message' => 'Table updated successfully',
            'data' => $table
        ]);
    }


    /* -------------------------
     DELETE (SOFT)
    --------------------------*/
    public function destroy(Table $table)
    {
        $table->delete();

        return response()->json([
            'message' => 'Table deleted successfully'
        ]);
    }

    /* -------------------------
     CHANGE STATUS (KDS / POS)
    --------------------------*/
    public function updateStatus(Request $request, Table $table)
    {
        $request->validate([
            'status' => 'required|in:available,occupied,reserved,disabled'
        ]);

        $table->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Status updated',
            'data' => $table
        ]);
    }

    public function getQrCode($tableId)
    {
        $table = Table::findOrFail($tableId);

        // Make sure token exists
        if (!$table->qr_token) {
            $table->qr_token = \Illuminate\Support\Str::random(10);
            $table->save();
        }

        // Correct URL for the QR code
        $url = 'https://inventory.nongofficial.store/mobile-menu/' . $table->qr_token;

        $qr = QrCode::size(250)->generate($url);

        return response($qr)->header('Content-Type', 'image/svg+xml');
    }

    public function getTableByToken($token)
    {
        $table = Table::where('qr_token', $token)->firstOrFail();
        $menuItems = Menu::all();

        return response()->json([
            'table' => $table,
            'menuItems' => $menuItems
        ]);
    }
}
