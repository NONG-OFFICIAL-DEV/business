<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryMenu extends Model
{
    use HasFactory;

    protected $table = 'category_menus';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    public function menus()
    {
        return $this->hasMany(Menu::class, 'menu_category_id');
    }
}
