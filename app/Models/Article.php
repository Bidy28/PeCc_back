<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    protected $fillable = [
        'titre',
        'description',
        'date',
        'service_id',
        'image',
    ];

    protected $casts = [
        // Renvoyé en "2026-09-21" et non en horodatage complet : c'est une
        // date de publication, pas un instant.
        'date' => 'date:Y-m-d',
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

    /** Le métier auquel l'article est rattaché. */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /** Du plus récent au plus ancien ; l'id départage deux mêmes dates. */
    public function scopeRecents($query)
    {
        return $query->orderByDesc('date')->orderByDesc('id');
    }
}
