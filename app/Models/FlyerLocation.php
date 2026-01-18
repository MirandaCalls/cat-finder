<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlyerLocation extends Model
{
    protected $fillable = [
        'latitude',
        'longitude',
        'address',
        'notes',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];
}
