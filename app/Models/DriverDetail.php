<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'vehicle_plate_number', 'status', 'delivery_zone'])]
class DriverDetail extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
