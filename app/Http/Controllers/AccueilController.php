<?php

namespace App\Http\Controllers;

use App\Models\Accueil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AccueilController extends Controller
{
    /**
     * Contenu de la page d'accueil. Route publique : le site vitrine l'appelle.
     */
    public function index()
    {
        return response()->json(Accueil::unique());
    }

    /**
     * Met à jour le titre, le sous-titre et éventuellement l'image.
     *
     * La requête arrive en multipart/form-data (à cause du fichier), donc
     * toutes les valeurs sont des chaînes — d'où "supprimer_image" traité
     * comme un booléen textuel.
     */
    public function mettreAJour(Request $request)
    {
        $donnees = $request->validate([
            'titre' => 'required|string|max:255',
            'sous_titre' => 'nullable|string|max:1000',
            // "image" vérifie le contenu réel du fichier, "mimes" l'extension :
            // les deux ensemble, jamais l'un sans l'autre.
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'supprimer_image' => 'nullable|boolean',
        ]);

        $accueil = Accueil::unique();
        $ancienneImage = $accueil->image;

        $accueil->titre = $donnees['titre'];
        $accueil->sous_titre = $donnees['sous_titre'] ?? null;

        if ($request->hasFile('image')) {
            // store() génère un nom aléatoire : pas de collision entre deux
            // fichiers du même nom, et le nom d'origine (accents, espaces,
            // caractères exotiques) n'atteint jamais le disque.
            $accueil->image = $request->file('image')->store('accueil', 'uploads');
        } elseif ($request->boolean('supprimer_image')) {
            $accueil->image = null;
        }

        $accueil->save();

        // On ne supprime l'ancien fichier qu'après un enregistrement réussi :
        // si la base échoue, l'image référencée existe toujours.
        if ($ancienneImage && $ancienneImage !== $accueil->image) {
            Storage::disk('uploads')->delete($ancienneImage);
        }

        Log::info('Accueil mis à jour', ['image' => $accueil->image]);

        return response()->json($accueil);
    }
}
