<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sale_id'        => 'required|exists:sales,id',
            'payment_method' => 'required|string',
            'amount'         => 'required|numeric|min:0.01',
            'reference_no'   => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {

            Payment::create([
                'sale_id'        => $request->sale_id,
                'payment_method' => $request->payment_method,
                'amount'         => $request->amount,
                'reference_no'   => $request->reference_no,
                'payment_status' => 'paid',
                'paid_at'        => now(),
            ]);

            // Optional: update sale status when fully paid
            $sale = Sale::find($request->sale_id);

            $paidAmount = Payment::where('sale_id', $sale->id)
                ->where('payment_status', 'paid')
                ->sum('amount');

            if ($paidAmount >= $sale->total_amount) {
                $sale->update(['status' => 'completed']);
            }
        });

        return response()->json([
            'message' => 'Payment recorded successfully'
        ], 201);
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
     * Get payments for a sale
     */
    public function bySale($saleId)
    {
        $payments = Payment::where('sale_id', $saleId)->get();

        return response()->json($payments);
    }

    /**
     * Daily sales report
     */
    public function dailyReport(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $report = Payment::select(
            'payment_method',
            DB::raw('SUM(amount) as total')
        )
            ->whereDate('paid_at', $date)
            ->where('payment_status', 'paid')
            ->groupBy('payment_method')
            ->get();

        return response()->json([
            'date' => $date,
            'data' => $report
        ]);
    }

    /**
     * Refund payment
     */
    public function refund($id)
    {
        $payment = Payment::findOrFail($id);

        $payment->update([
            'payment_status' => 'refunded'
        ]);

        return response()->json([
            'message' => 'Payment refunded successfully'
        ]);
    }
}
