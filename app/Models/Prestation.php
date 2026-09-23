<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestation extends Model
{
    protected $fillable = [
        'service_id',
        'nom',
        'cout',
        'prix_minimum',
        'prix_maximum',
    ];

    protected $casts = [
        'cout' => 'float',
        'prix_minimum' => 'float',
        'prix_maximum' => 'float',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
