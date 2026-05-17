<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['user_id', 'gender', 'age', 'weight_kg', 'height_cm', 'activity_level', 'diet_goal', 'daily_calorie_target', 'allergies'])]
class UserProfile extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
