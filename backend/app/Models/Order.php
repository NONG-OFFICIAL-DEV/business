<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_no',
        'table_id',
        'status',
        'kitchen_status'
    ];

    /**
     * Order belongs to a table
     */
    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    /**
     * Order has many items
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function sale()
    {
        return $this->hasOne(Sale::class);
    }
}
