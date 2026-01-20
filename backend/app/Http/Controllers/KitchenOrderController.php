<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KitchenOrderController extends Controller
{
     // GET /api/kitchen/orders
    public function index()
    {
        return Order::with([
            'table:id,table_number',
            'items.menuItem:id,name'
        ])
        ->whereIn('kitchen_status', ['pending', 'preparing', 'ready'])
        ->orderBy('created_at')
        ->get()
        ->map(function ($order) {
            return [
                'id' => $order->id,
                'order_no' => $order->order_no,
                'kitchen_status' => $order->kitchen_status,
                'table' => $order->table,
                'items' => $order->items->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->menuItem->name,
                    'quantity' => $item->quantity,
                    'note' => $item->note
                ])
            ];
        });
    }

    // PATCH /api/kitchen/orders/{order}/start
    public function start(Order $order)
    {
        $order->update(['kitchen_status' => 'preparing']);
        $order->items()->update(['status' => 'preparing']);

        return response()->json(['success' => true]);
    }

    // PATCH /api/kitchen/orders/{order}/ready
    public function ready(Order $order)
    {
        $order->update(['kitchen_status' => 'ready']);
        $order->items()->update(['status' => 'ready']);

        return response()->json(['success' => true]);
    }
}
