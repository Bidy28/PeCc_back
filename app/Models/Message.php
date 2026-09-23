<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'nom',
        'email',
        'telephone',
        'message',
        'service_id',
        'traite',
    ];

    protected $casts = [
        // Sans ce cast, MySQL renvoie 0/1 et le front reçoit un nombre
        // là où il attend un booléen.
        'traite' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
