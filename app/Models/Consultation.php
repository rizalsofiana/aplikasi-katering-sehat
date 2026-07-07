<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['customer_id', 'nutritionist_id', 'topic', 'status'])]
class Consultation extends Model
{
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function nutritionist()
    {
        return $this->belongsTo(User::class, 'nutritionist_id');
    }

    public function messages()
    {
        return $this->hasMany(ConsultationMessage::class);
    }
}
