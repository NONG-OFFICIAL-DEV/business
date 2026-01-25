<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'sale_date',
        'total_amount',
        'invoice_no',
        'notes',
        'discount',
        'tax_amount',
        'status',
        'cashier_id',
        'payment_method',
        'order_id',
    ];

    protected $casts = [
        'sale_date' => 'datetime', // This ensures ->format() works
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($sale) {
            // Example: INV-20260124-00001
            $invoice = 'INV-' .
                now()->format('Ymd') . '-' .
                str_pad($sale->id, 5, '0', STR_PAD_LEFT);

            $sale->update([
                'invoice_no' => $invoice
            ]);
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
