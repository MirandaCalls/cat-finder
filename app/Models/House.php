<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    protected $fillable = [
        'latitude',
        'longitude',
        'address',
        'flyer_left',
        'talked_to_owners',
        'notes',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'flyer_left' => 'boolean',
        'talked_to_owners' => 'boolean',
    ];
}
