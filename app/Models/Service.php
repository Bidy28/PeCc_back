<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'nom',
        'slug',
        'description',
        'icone',
        'couleur',
        'actif',
        'ordre',
        'prix',
        'elements',
    ];

    protected $casts = [
        'prix' => 'float',
        'actif' => 'boolean',
        'ordre' => 'integer',
        'elements' => 'array',
    ];

    public function prestations()
    {
        return $this->hasMany(Prestation::class);
    }

    /** Métiers visibles sur le site public, dans l'ordre choisi par l'admin. */
    public function scopeVisibles($query)
    {
        return $query->where('actif', true);
    }

    /** Ordre d'affichage : le champ `ordre`, puis le nom pour départager. */
    public function scopeOrdonnes($query)
    {
        return $query->orderBy('ordre')->orderBy('nom');
    }
}
