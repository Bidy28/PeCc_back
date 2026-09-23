<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /**
     * Retourne la liste des métiers avec leur présentation et leurs prix,
     * dans l'ordre d'affichage choisi depuis l'admin.
     *
     * `?actifs=1` ne renvoie que les métiers visibles : c'est ce qu'appelle
     * le site public. Le filtrage est fait en SQL, pas côté navigateur —
     * sinon les métiers masqués partiraient quand même au visiteur, lisibles
     * dans l'onglet réseau. L'admin, lui, appelle la route sans paramètre
     * pour voir aussi les métiers masqués.
     */
    public function index(Request $request)
    {
        $services = Service::query()
            ->when($request->boolean('actifs'), fn ($q) => $q->visibles())
            ->select([
                'id',
                'nom',
                'slug',
                'description',
                'icone',
                'couleur',
                'actif',
                'ordre',
                'prix',
                'elements',
            ])
            ->with('prestations:id,service_id,nom,cout,prix_minimum,prix_maximum')
            ->ordonnes()
            ->get();

        return response()->json($services);
    }

    /**
     * Bascule la visibilité d'un métier sur le site public : masqué s'il est
     * visible, affiché s'il est masqué.
     *
     * On renvoie le métier à jour plutôt qu'un simple message : l'admin
     * affiche ainsi l'état réel en base au lieu de supposer le résultat.
     */
    public function masquer(Service $service)
    {
        $service->update(['actif' => ! $service->actif]);

        return response()->json($service->load('prestations'));
    }

    /**
     * Crée un métier.
     *
     * Le prix démarre à 0 : il se renseigne depuis l'écran Tarifs, en même
     * temps que les prestations. L'ordre d'affichage est placé en fin de liste.
     */
    public function ajouter(Request $request)
    {
        $donnees = $request->validate([
            'nom' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('services', 'slug'),
            ],
            'description' => 'nullable|string|max:2000',
            'icone' => 'required|string|max:40',
            'couleur' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'actif' => 'required|boolean',
            'ordre' => 'nullable|integer|min:0|max:65535',
        ]);

        if (blank($donnees['slug'] ?? null)) {
            $donnees['slug'] = $this->slugDisponible($donnees['nom']);
        }

        // Sans valeur d'ordre, le nouveau métier se place après les autres.
        $donnees['ordre'] ??= (int) Service::max('ordre') + 1;

        // Colonnes obligatoires en base et non saisies sur cet écran.
        $donnees['prix'] = 0;
        $donnees['elements'] = [];

        $service = Service::create($donnees);

        return response()->json($service->load('prestations'), 201);
    }

    /**
     * Supprime un métier, et avec lui ses prestations : la clé étrangère de
     * la table `prestations` est en cascadeOnDelete. L'admin en est prévenu
     * avant de confirmer, le compte étant renvoyé par index().
     */
    public function supprimer(Service $service)
    {
        $service->delete();

        return response()->json(['message' => 'Métier supprimé.']);
    }

    /**
     * Met à jour un métier.
     *
     * Le prix n'est pas modifiable ici : il l'est depuis l'écran Tarifs, via
     * PrestationController::synchroniser. Deux écrans qui écrivent le même
     * champ finiraient par se contredire.
     */
    public function modifier(Request $request, Service $service)
    {
        $donnees = $request->validate([
            'nom' => 'required|string|max:255',
            // Le slug doit rester unique, mais la ligne en cours d'édition
            // porte déjà le sien : on l'exclut de la vérification.
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('services', 'slug')->ignore($service->id),
            ],
            'description' => 'nullable|string|max:2000',
            'icone' => 'required|string|max:40',
            'couleur' => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'actif' => 'required|boolean',
            'ordre' => 'nullable|integer|min:0|max:65535',
        ]);

        // Slug laissé vide : on le dérive du nom. On ne le régénère jamais
        // tout seul quand il existe déjà — une URL publique doit rester stable.
        if (blank($donnees['slug'] ?? null)) {
            $donnees['slug'] = $this->slugDisponible($donnees['nom'], $service->id);
        }

        $donnees['ordre'] ??= $service->ordre;

        $service->update($donnees);

        return response()->json($service->load('prestations'));
    }

    /**
     * Dérive un slug du nom et le suffixe tant qu'il est déjà pris,
     * en ignorant la ligne en cours d'édition.
     */
    private function slugDisponible(string $nom, ?int $idIgnore = null): string
    {
        $base = Str::slug($nom) ?: 'metier';
        $slug = $base;
        $suffixe = 2;

        while (
            Service::where('slug', $slug)
                ->when($idIgnore, fn ($q) => $q->where('id', '!=', $idIgnore))
                ->exists()
        ) {
            $slug = $base.'-'.$suffixe++;
        }

        return $slug;
    }
}
