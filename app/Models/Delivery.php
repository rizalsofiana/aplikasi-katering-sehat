<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['subscription_id', 'menu_id', 'driver_id', 'delivery_date', 'meal_time', 'status'])]
class Delivery extends Model
{
    //

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
