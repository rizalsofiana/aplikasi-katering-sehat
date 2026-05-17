<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['menu_id', 'calories', 'protein_g', 'carbs_g', 'fat_g'])]
class MenuNutrition extends Model
{
    //

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
