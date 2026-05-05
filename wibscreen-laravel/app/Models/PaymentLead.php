<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLead extends Model
{
    protected $fillable = [
        'user_id',
        'plan_slug',
        'status',
        'last_notified_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
