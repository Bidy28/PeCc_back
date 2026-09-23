<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Accueil extends Model
{
    /**
     * Laravel pluralise "Accueil" en "accueils" : le nom de table tombe juste,
     * mais on l'écrit pour que ce soit explicite.
     */
    protected $table = 'accueils';

    protected $fillable = [
        'titre',
        'sous_titre',
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

    /**
     * Le site n'a qu'une seule page d'accueil : on récupère toujours
     * la première ligne, créée par la migration.
     */
    public static function unique(): self
    {
        return static::firstOrCreate([], [
            'titre' => 'Bienvenue',
            'sous_titre' => null,
            'image' => null,
        ]);
    }
}
