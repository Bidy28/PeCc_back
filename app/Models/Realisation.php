<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Realisation extends Model
{
    protected $fillable = [
        'titre',
        'description',
        'ville',
        'service_id',
        'image',
    ];

    /**
     * Ajoute "image_url" à chaque réponse JSON : le front reçoit le chemin
     * brut (utile pour le réenvoyer) ET l'URL complète (utile pour l'afficher).
     */
    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return Storage::disk('uploads')->url($this->image);
    }

    /** Le métier auquel le chantier est rattaché. */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /** Les plus récents d'abord : une galerie montre les derniers chantiers. */
    public function scopeRecentes($query)
    {
        return $query->orderByDesc('created_at')->orderByDesc('id');
    }
}
