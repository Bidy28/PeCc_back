<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestation;
use App\Models\Service;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PrestationController extends Controller
{
    public function index()
    {
        // Récupérer les prestations avec leurs services associés
        $prestations = Prestation::with('service')->get();

        return response()->json($prestations);
    }

    /**
     * Remplace la liste complète des prestations d'un service par celle reçue :
     * les lignes avec un id sont mises à jour, les autres créées, et celles
     * absentes du tableau sont supprimées. Le tout dans une transaction.
     */
    public function synchroniser(Request $request, Service $service)
    {
        $donnees = $request->validate([
            'prix' => 'required|numeric|min:0',
            'prestations' => 'present|array',
            'prestations.*.id' => 'nullable|integer',
            'prestations.*.nom' => 'required|string|max:255',
            'prestations.*.prix_minimum' => 'required|numeric|min:0',
            'prestations.*.prix_maximum' => 'required|numeric|min:0|gte:prestations.*.prix_minimum',
        ]);

        Log::info('Synchronisation des prestations du service ' . $service->id, $donnees);

        DB::transaction(function () use ($service, $donnees) {
            Log::info('Début de la transaction pour le service ' . $service->id);
            $prixActuel = number_format((float) $service->prix, 2, '.', '');
            $nouveauPrix = number_format((float) $donnees['prix'], 2, '.', '');

            if ($prixActuel !== $nouveauPrix) {
                $service->update(['prix' => $donnees['prix']]);
            }

            $conservees = [];

            foreach ($donnees['prestations'] as $ligne) {
                $valeurs = Arr::only($ligne, ['nom', 'prix_minimum', 'prix_maximum']);

                // On cherche l'id parmi les prestations du service : un id
                // appartenant à un autre service est traité comme une création.
                $prestation = isset($ligne['id'])
                    ? $service->prestations()->find($ligne['id'])
                    : null;

                if ($prestation) {
                    $prestation->update($valeurs);
                } else {
                    $prestation = $service->prestations()->create($valeurs);
                }

                $conservees[] = $prestation->id;
            }

            // Tableau vide = toutes les prestations du service sont supprimées.
            $service->prestations()->whereNotIn('id', $conservees)->delete();
        });

        return response()->json($service->load('prestations'));
    }
}
