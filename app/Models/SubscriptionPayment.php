<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['subscription_id', 'invoice_number', 'amount', 'payment_method', 'status', 'paid_at'])]
class SubscriptionPayment extends Model
{
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
