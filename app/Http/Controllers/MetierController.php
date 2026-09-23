<?php

namespace App\Http\Controllers;

use App\Models\Metier;
use Illuminate\Http\Request;

class MetierController extends Controller
{
    /**
     * Liste des détails de savoir-faire.
     * Route publique : la pop-up « En savoir plus » et la page métier l'appellent.
     *
     * `?service_id=4` ne renvoie que ceux d'un service donné — c'est le cas
     * d'usage normal, une page métier n'affiche jamais les autres.
     * `?actifs=1` écarte ceux dont le service est masqué, comme ailleurs.
     */
    public function index(Request $request)
    {
        $request->validate([
            'service_id' => 'nullable|integer|exists:services,id',
        ]);

        $metiers = Metier::query()
            ->when(
                $request->filled('service_id'),
                fn ($q) => $q->where('service_id', $request->integer('service_id'))
            )
            ->when(
                $request->boolean('actifs'),
                fn ($q) => $q->whereHas('service', fn ($q) => $q->visibles())
            )
            ->with('service:id,nom,slug,couleur,icone')
            ->orderBy('id')
            ->get();

        return response()->json($metiers);
    }

    public function ajouter(Request $request)
    {
        $metier = Metier::create($this->valider($request));

        return response()->json($metier->load('service:id,nom,slug,couleur,icone'), 201);
    }

    public function modifier(Request $request, Metier $metier)
    {
        $metier->update($this->valider($request));

        return response()->json($metier->load('service:id,nom,slug,couleur,icone'));
    }

    public function supprimer(Metier $metier)
    {
        $metier->delete();

        return response()->json(['message' => 'Détail supprimé.']);
    }

    /**
     * Règles communes à la création et à la modification : les factoriser
     * évite qu'elles divergent au fil des corrections.
     */
    private function valider(Request $request): array
    {
        return $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'service_id' => 'required|exists:services,id',
        ]);
    }
}
