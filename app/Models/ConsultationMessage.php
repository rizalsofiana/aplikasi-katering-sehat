<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['consultation_id', 'sender_type', 'message'])]
class ConsultationMessage extends Model
{
    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}
