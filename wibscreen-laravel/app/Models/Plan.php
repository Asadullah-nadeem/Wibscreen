<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name', 'slug', 'price_monthly', 'price_yearly', 'description', 'features', 'is_popular'];

    protected $casts = [
        'features' => 'array',
        'is_popular' => 'boolean',
    ];
}
