<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['menu_id', 'calories', 'protein_g', 'carbs_g', 'fat_g'])]
#[Table('menu_nutritions')]
class MenuNutrition extends Model
{
    //

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id', 'menu_id');
    }
}
