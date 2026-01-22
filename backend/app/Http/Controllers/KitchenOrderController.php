<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KitchenOrderController extends Controller
{
     // GET /api/kitchen/orders
    public function index()
    {
        return Order::with([
            'table:id,table_number',
            'items.menu:id,name'
        ])
        ->whereIn('kitchen_status', ['pending', 'preparing', 'ready'])
        ->orderBy('created_at')
        ->get()
        ->map(function ($order) {
            return [
                'id' => $order->id,
                'order_no' => $order->order_no,
                'kitchen_status' => $order->kitchen_status,
                'order_time' => $order->created_at,
                'table' => $order->table,
                'items' => $order->items->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->menu->name,
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

    public function markServed(Order $order)
    {
        $order->update([
            'status' => 'served'
        ]);

        return response()->json(['success' => true]);
    }

    public function stream()
    {
        return response()->stream(function () {
            while (true) {

                $orders = Order::with([
                    'table:id,table_number',
                    'items.menu:id,name'
                ])
                ->whereIn('kitchen_status', ['pending', 'preparing', 'ready'])
                ->orderBy('created_at')
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_no' => $order->order_no,
                        'kitchen_status' => $order->kitchen_status,
                        'order_time' => $order->created_at,
                        'table' => $order->table,
                        'items' => $order->items->map(fn ($item) => [
                            'id' => $item->id,
                            'name' => $item->menu->name,
                            'quantity' => $item->quantity,
                            'note' => $item->note
                        ])
                    ];
                });

                echo "event: orders\n";
                echo "data: " . json_encode($orders) . "\n\n";

                ob_flush();
                flush();

                sleep(3); // push every 3 seconds
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }
}
