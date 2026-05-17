<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['package_name', 'duration_type', 'total_days', 'price'])]
class Package extends Model
{
    //

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
