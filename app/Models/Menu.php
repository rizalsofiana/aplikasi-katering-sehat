<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'image_path', 'is_available'])]
class Menu extends Model
{
    //

    public function nutrition()
    {
        return $this->hasOne(MenuNutrition::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
}
