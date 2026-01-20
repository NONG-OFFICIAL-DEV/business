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

        // FIX: Decode JSON string if needed
        if (is_string($variants)) {
            $variants = json_decode($variants, true);
        }

        if (is_array($variants) && count($variants) > 0) {
            $menu->has_variants = true;
            $menu->variants()->delete();

            foreach ($variants as $variant) {
                $menu->variants()->create([
                    'name'  => $variant['name'],
                    'price' => $variant['price'],
                ]);
            }
        } else {
            $menu->has_variants = false;
        }

        $menu->save();


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
