<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with([
            'table:id,table_number',
            'items:id,order_id,menu_id,quantity,price,note',
            'items.menu:id,name'
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(
            $orders->map(fn($order) => [
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'table' => $order->table?->table_number,
                'item_count' => $order->items->sum('quantity'),
                // 'status' => $order->status,
                'total' => $order->items->sum(fn($i) => $i->quantity * $i->price),
                'created_at' => $order->created_at,
                'items' => $order->items->map(fn($item) => [
                    'id' => $item->id,
                    'menu' => $item->menu->name,
                    'qty' => $item->quantity,
                    'price' => $item->price,
                    'note' => $item->note,
                    // 'kitchen_status' => $item->kitchen_status,
                ]),
            ])
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.note' => 'nullable|string|max:255',
            'items.*.price' => 'nullable',
        ]);

        // Create order
        $order = Order::create([
            'order_no' => 'ORD-' . now()->format('YmdHis'), // simple order_no
            'table_id' => $request->table_id,
            'status' => 'new',
            'kitchen_status' => 'pending',
        ]);

        // Add order items
        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['menu_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'] ?? null,
                'note' => $item['note'] ?? null,
                'status' => 'pending',
            ]);
        }

        // Load relations for response
        $order->load('table', 'items.menu');

        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
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

    /**
     * Get active order by table number
     */
    public function getByTable(string $tableNumber)
    {
        $table = Table::where('table_number', trim($tableNumber))
            ->with(['activeOrder.items.menu'])
            ->first();

        if (!$table) {
            return response()->json([
                'error' => 'Table not found'
            ], 404);
        }

        if (!$table->activeOrder) {
            return response()->json(null, 204); // correct
        }

        return response()->json([
            'order_id' => $table->activeOrder->id,
            'order_no' => $table->activeOrder->order_no,
            'status' => $table->activeOrder->status,
            'kitchen_status' => $table->activeOrder->kitchen_status,
            'items' => $table->activeOrder->items->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->menu->name,
                'price' => $item->menu->price,
                'qty' => $item->quantity,
                'status' => $item->status,
                'note' => $item->note,
            ])
        ]);
    }
}
