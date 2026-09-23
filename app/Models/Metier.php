<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Metier extends Model
{
    protected $fillable = [
        'titre',
        'description',
        'service_id',
    ];

    /** Le service auquel ce détail est rattaché. */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
