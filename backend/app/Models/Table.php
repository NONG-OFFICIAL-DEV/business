<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_number',
        'is_available'
    ];

    /**
     * A table can have many orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get active order for this table
     */
    public function activeOrder()
    {
        return $this->hasOne(Order::class)
            ->whereIn('kitchen_status', ['pending', 'preparing', 'ready'])
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->latest();
    }
}
