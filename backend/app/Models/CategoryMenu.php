<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryMenu extends Model
{
    use HasFactory;

    protected $table = 'menu_categories';

    protected $fillable = [
        'name',
        'order',
        'status',
    ];

    public function menus()
    {
        return $this->hasMany(Menu::class, 'menu_category_id');
    }
}
