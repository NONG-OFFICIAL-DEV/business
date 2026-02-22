<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Sale::with('items.product')->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.menu_id'    => 'nullable|exists:menus,id',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($data) {

            $total = collect($data['items'])
                ->sum(fn($i) => $i['qty'] * $i['price']);

            // 1. Find the last queue number for today
            $lastQueue = Sale::whereDate('sale_date', today())->max('queue_number');
            $nextQueue = $lastQueue ? $lastQueue + 1 : 1;

            $sale = Sale::create([
                'sale_date' => now(),
                'total_amount' => $total,
                'queue_number' => $nextQueue,
                'status' => 'paid',
            ]);

            foreach ($data['items'] as $item) {

                // 🔁 Resolve sellable model
                if (!empty($item['product_id'])) {
                    $sellable = Product::findOrFail($item['product_id']);
                } else if (!empty($item['menu_id'])) {
                    $sellable = Menu::findOrFail($item['menu_id']);
                } else {
                    throw new \Exception('Invalid sale item');
                }

                // 🧾 Create sale item
                $sale->items()->create([
                    'sellable_id' => $sellable->id,
                    'sellable_type' => get_class($sellable),
                    'name' => $sellable->name,      // snapshot
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'total' => $item['qty'] * $item['price'],
                ]);

                // 📦 Reduce stock ONLY for products
                if ($sellable instanceof Product) {
                    DB::table('stocks')
                        ->where('product_id', $sellable->id)
                        ->decrement('quantity', $item['qty']);
                }
            }

            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'invoice_no' => $sale->invoice_no ?? null,
                'total' => $sale->total_amount,
            ], 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Sale::with('items.product')->findOrFail($id);
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

    public function getReport(Request $request)
    {
        $from = $request->input('from', now()->subDays(30)->startOfDay());
        $to = $request->input('to', now()->endOfDay());

        // 1. Summary Metrics with Comparison logic (placeholder for trend)
        $metrics = Sale::whereBetween('created_at', [$from, $to])
            ->selectRaw('SUM(total_amount) as revenue, COUNT(*) as count, AVG(total_amount) as avg')
            ->first();

        // 2. Revenue Trend (Daily)
        $chartData = Sale::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total')
        )
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('date')
            ->get();

        // 3. Peak Hours (Crucial for Coffee/Restaurant)
        $peakHours = Sale::select(
            DB::raw('EXTRACT(HOUR FROM created_at) as hour'),
            DB::raw('COUNT(*) as count')
        )
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // 4. Top Selling Items (What is making money?)
        $topItems = DB::table('sale_items')
            ->select('sellable_type', 'sellable_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(price*quantity) as total_amount'))
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$from, $to])
            ->groupBy('sellable_type', 'sellable_id')
            ->get();


        return response()->json([
            'summary' => [
                'total_revenue' => $metrics->revenue ?? 0,
                'total_sales'   => $metrics->count ?? 0,
                'avg_value'     => round($metrics->avg ?? 0, 2),
            ],
            'charts' => [
                'revenue' => [
                    'labels' => $chartData->pluck('date'),
                    'datasets' => [['label' => 'Revenue', 'data' => $chartData->pluck('total')]]
                ],
                'hours' => [
                    'labels' => $peakHours->pluck('hour')->map(fn($h) => $h . ":00"),
                    'values' => $peakHours->pluck('count')
                ]
            ],
            'top_items' => $topItems,
            'table_data' => Sale::withCount('items')->orderBy('created_at', 'desc')->paginate(15)
        ]);
    }
    public function topMenusReport(Request $request)
    {
        $from = $request->input('from', now()->subMonth()->startOfDay());
        $to = $request->input('to', now()->endOfDay());
        $limit = $request->input('limit', 10); // top 10 by default

        // Join sale_items with sales and filter by menu items
        $topMenus = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sale_items.sellable_type', '=', 'App\\Models\\Menu')
            ->whereBetween('sales.sale_date', [$from, $to])
            ->select(
                'sale_items.sellable_id as menu_id',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.quantity * sale_items.price) as total_revenue')
            )
            ->groupBy('sale_items.sellable_id')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();

        // Add menu names
        $topMenus->transform(function ($item) {
            $menu = Menu::find($item->menu_id);
            $item->menu_name = $menu?->name ?? 'Unknown';
            return $item;
        });

        return response()->json([
            'data' => $topMenus
        ]);
    }
    // app/Http/Controllers/SaleController.php
    // for coffee and mart print during pay or print and pay emidatly
    // public function invoice(Sale $sale)
    // {
    //     // Load items (polymorphic: Product or Menu)
    //     $sale->load('items.sellable');

    //     // Calculate total
    //     $total = $sale->items->sum(fn($item) => $item->quantity * $item->price);

    //     return view('sales.invoice', compact('sale', 'total'));
    // }

    public function invoice(Sale $sale)
    {
        $sale->load('items.sellable');
        $total = $sale->items->sum(fn($item) => $item->quantity * $item->price);

        // Set options for better performance and image loading
        $pdf = Pdf::loadView('sales.invoice', compact('sale', 'total'))
            ->setPaper([0, 0, 226, 600], 'portrait') // 80mm width (approx 226pt)
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true, // Allows loading images from URL
                'defaultFont' => 'NotoSansKhmer'
            ]);

        return $pdf->download("invoice-{$sale->id}.pdf");
    }

    // Restaurant: PRINT BILL And Pay after
    public function printBill(Order $order, SaleService $saleService)
    {
        $sale = $saleService->createFromOrder($order);

        return response()->json([
            'message' => 'Sale created successfully',
            'sale_id' => $sale->id,
            'invoice_url' => url("/download/{$sale->id}/invoice")
        ]);
        // return redirect()->route('sales.invoice', $sale);
    }

    // Coffee / Mart: PAY & PRINT
    public function payAndPrint(Request $request, Order $order, SaleService $saleService)
    {
        $sale = $saleService->createFromOrder($order);

        $sale->payments()->create([
            'method' => $request->method,
            'amount' => $sale->total,
        ]);

        $sale->update([
            'status' => 'completed',
            'payment_status' => 'paid'
        ]);

        return redirect()->route('sales.invoice', $sale);
    }

    // Restaurant: PAY AFTER BILL
    public function paySale(Request $request, Sale $sale)
    {
        $sale->payments()->create([
            'method' => $request->method,
            'amount' => $request->amount,
        ]);

        if ($sale->payments->sum('amount') >= $sale->total) {
            $sale->update([
                'status' => 'completed',
                'payment_status' => 'paid'
            ]);
        }

        return response()->json($sale->load('payments'));
    }
}
