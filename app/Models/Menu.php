<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'image_path', 'is_available'])]
#[Table('menus')]
class Menu extends Model
{
    public function nutrition()
    {
        return $this->hasOne(MenuNutrition::class, 'menu_id', 'id');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'menu_id', 'id');
    }
}
