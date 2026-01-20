<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{

    protected $fillable = [
        'category_id',
        'name',
        'price',
        'image',
        'has_variants',
        'status'
    ];

   public static function store($request, $id = null)
    {
        // Basic menu data
        $menuData = $request->only(
            'category_id',
            'name',
            'price',
            'status'
        );

        if ($id) {
            $menu = self::find($id);
            if (!$menu) {
                return response()->json(['error' => 'Menu item not found'], 404);
            }
            $menu->update($menuData);
        } else {
            $menu = self::create($menuData);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $menu->image = $request->file('image')->store('menus', 'public');
            $menu->save();
        }

        // Sync variants (sizes)
        $variants = $request->input('sizes', []);

        if (!empty($variants)) {
            $menu->has_variants = true;
            $menu->variants()->delete(); // Remove old variants

            foreach ($variants as $variant) {
                $menu->variants()->create($variant);
            }
        } else {
            $menu->has_variants = false;
        }

        $menu->save(); // IMPORTANT: persist has_variants

        return response()->json([
            'success' => true,
            'data' => $menu->load('variants')
        ], $id ? 200 : 201);
    }


    public function variants()
    {
        return $this->hasMany(MenuVariant::class);
    }

    // public function recipes()
    // {
    //     return $this->hasMany(MenuRecipe::class);
    // }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
