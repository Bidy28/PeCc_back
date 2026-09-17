<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'nom',
        'prix',
        'elements',
    ];

    protected $casts = [
        'prix' => 'float',
        'elements' => 'array',
    ];
}
