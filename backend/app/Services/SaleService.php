<?php
namespace App\Services;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Sale;

class SaleService
{
    public function createFromOrder(Order $order): Sale
    {
        // Prevent duplicate sale
        if ($order->sale) {
            return $order->sale;
        }

        $total = $order->items->sum(fn ($i) => $i->quantity * $i->price);

        $sale = Sale::create([
            'sale_no' => 'SAL-' . now()->format('YmdHis'),
            'order_id' => $order->id,
            'total' => $total,
            'status' => 'open',
            'payment_status' => 'unpaid',
        ]);

        foreach ($order->items as $item) {
            $sale->items()->create([
                'sellable_type' => Menu::class,
                'sellable_id' => $item->menu_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
            ]);
        }

        return $sale;
    }
}
