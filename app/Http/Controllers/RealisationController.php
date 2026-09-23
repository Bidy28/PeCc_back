<?php

namespace App\Http\Controllers;

use App\Models\Realisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RealisationController extends Controller
{
    /**
     * Liste des chantiers, du plus récent au plus ancien.
     * Route publique : la galerie du site l'appelle.
     */
    public function index()
    {
        $realisations = Realisation::query()
            ->when(request()->boolean('actifs'), fn ($q) => $q->whereHas('service', fn ($q) => $q->visibles()))
            ->select([
                'id',
                'titre',
                'description',
                'ville',
                'service_id',
                'image',
                'created_at',
            ])
            ->with('service:id,nom,slug,couleur,icone')
            ->recentes()
            ->get();

        return response()->json($realisations);
    }

    /**
     * Crée un chantier, avec son image si elle est fournie.
     *
     * La requête arrive en multipart/form-data à cause du fichier : toutes
     * les valeurs sont donc des chaînes côté serveur.
     */
    public function ajouter(Request $request)
    {
        $donnees = $this->valider($request);

        if ($request->hasFile('image')) {
            // store() génère un nom aléatoire : pas de collision entre deux
            // fichiers du même nom, et le nom d'origine (accents, espaces,
            // caractères exotiques) n'atteint jamais le disque.
            $donnees['image'] = $request->file('image')->store('realisations', 'uploads');
        }

        $realisation = Realisation::create($donnees);

        Log::info('Réalisation créée', ['id' => $realisation->id]);

        return response()->json($realisation->load('service:id,nom,slug,couleur,icone'), 201);
    }

    /**
     * Met à jour un chantier.
     *
     * POST et non PUT : PHP ne décode pas le corps multipart sur une requête
     * PUT, le fichier n'arriverait jamais jusqu'ici.
     */
    public function modifier(Request $request, Realisation $realisation)
    {
        $donnees = $this->valider($request);
        $ancienneImage = $realisation->image;

        if ($request->hasFile('image')) {
            $donnees['image'] = $request->file('image')->store('realisations', 'uploads');
        } elseif ($request->boolean('supprimer_image')) {
            $donnees['image'] = null;
        }

        $realisation->update($donnees);

        // On ne supprime l'ancien fichier qu'après un enregistrement réussi :
        // si la base échoue, l'image référencée existe toujours.
        if ($ancienneImage && $ancienneImage !== $realisation->image) {
            Storage::disk('uploads')->delete($ancienneImage);
        }

        return response()->json($realisation->load('service:id,nom,slug,couleur,icone'));
    }

    /**
     * Supprime un chantier et le fichier image qui lui est rattaché : sans ça,
     * le disque se remplit de fichiers que plus rien ne référence.
     */
    public function supprimer(Realisation $realisation)
    {
        $image = $realisation->image;

        $realisation->delete();

        if ($image) {
            Storage::disk('uploads')->delete($image);
        }

        return response()->json(['message' => 'Réalisation supprimée.']);
    }

    /**
     * Règles communes à la création et à la modification.
     *
     * "image" vérifie le contenu réel du fichier, "mimes" son extension :
     * les deux ensemble, jamais l'un sans l'autre.
     */
    private function valider(Request $request): array
    {
        return $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'ville' => 'nullable|string|max:255',
            'service_id' => 'required|exists:services,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);
    }
}
