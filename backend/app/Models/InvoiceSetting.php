<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceSetting extends Model
{
    protected $fillable = [
        'store_name',
        'logo_url',
        'address',
        'phone',
        'tax_number',
        'footer_text',
    ];
}
