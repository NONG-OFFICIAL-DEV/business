<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'sale_id',
        'payment_method',
        'amount',
        'payment_status',
        'reference_no',
        'paid_at',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
