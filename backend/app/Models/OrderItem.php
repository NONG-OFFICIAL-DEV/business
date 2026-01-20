<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_id',
        'quantity',
        'note',
        'status'
    ];

    /**
     * Item belongs to an order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Item belongs to a menu item
     */
    public function menuItem()
    {
        return $this->belongsTo(Menu::class ,'menu_id');
    }
}
