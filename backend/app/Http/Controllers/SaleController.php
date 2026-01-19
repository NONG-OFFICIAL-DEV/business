<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Sale::with('items.product')->latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            // 'sale_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($data) {
            // total = sum of items
            $total = collect($data['items'])->sum(fn($i) => $i['qty'] * $i['price']);

            $sale = Sale::create([
                'sale_date' => now(),
                'total_amount' => $total,
            ]);

            foreach ($data['items'] as $item) {
                $sale->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                ]);

                // reduce stock
                DB::table('stocks')
                    ->where('product_id', $item['product_id'])
                    ->decrement('quantity', $item['qty']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully.',
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
        // Set date filters
        $days = $request->input('days', 30);
        $startDate = Carbon::now()->subDays($days);

        // 1. Calculate Summary Metrics
        $metricsData = Sale::where('created_at', '>=', $startDate)
            ->selectRaw('SUM(total_amount) as revenue, COUNT(*) as count, AVG(total_amount) as avg')
            ->first();

        // 2. Get Chart Data (Daily Revenue)
        $chartData = Sale::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 3. Get Detailed List with Items count
        // We use withCount to show how many items were in each sale
        $sales = Sale::withCount('items')
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'summary' => [
                'total_revenue' => number_format($metricsData->revenue, 2),
                'total_sales'   => $metricsData->count,
                'avg_value'     => number_format($metricsData->avg, 2),
            ],
            'chart' => [
                'labels' => $chartData->pluck('date'),
                'values' => $chartData->pluck('total'),
            ],
            'table_data' => $sales
        ]);
    }
}
