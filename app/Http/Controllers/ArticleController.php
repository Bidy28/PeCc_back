<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Liste des articles, du plus récent au plus ancien.
     * Route publique : le blog du site l'appelle.
     *
     * `?actifs=1` écarte les articles dont le métier est masqué, comme pour
     * les réalisations. Le filtrage est fait en SQL : ils ne partent pas au
     * navigateur. L'admin appelle sans paramètre pour tout voir.
     */
    public function index()
    {
        $articles = Article::query()
            ->when(
                request()->boolean('actifs'),
                fn ($q) => $q->whereHas('service', fn ($q) => $q->visibles())
            )
            ->with('service:id,nom,slug,couleur,icone')
            ->recents()
            ->get();

        return response()->json($articles);
    }

    /**
     * Crée un article, avec son image de couverture si elle est fournie.
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
            $donnees['image'] = $request->file('image')->store('articles', 'uploads');
        }

        $article = Article::create($donnees);

        Log::info('Article créé', ['id' => $article->id]);

        return response()->json($article->load('service:id,nom,slug,couleur,icone'), 201);
    }

    /**
     * Met à jour un article.
     *
     * POST et non PUT : PHP ne décode pas le corps multipart sur une requête
     * PUT, le fichier n'arriverait jamais jusqu'ici.
     */
    public function modifier(Request $request, Article $article)
    {
        $donnees = $this->valider($request);
        $ancienneImage = $article->image;

        if ($request->hasFile('image')) {
            $donnees['image'] = $request->file('image')->store('articles', 'uploads');
        } elseif ($request->boolean('supprimer_image')) {
            $donnees['image'] = null;
        }

        $article->update($donnees);

        // On ne supprime l'ancien fichier qu'après un enregistrement réussi :
        // si la base échoue, l'image référencée existe toujours.
        if ($ancienneImage && $ancienneImage !== $article->image) {
            Storage::disk('uploads')->delete($ancienneImage);
        }

        return response()->json($article->load('service:id,nom,slug,couleur,icone'));
    }

    /**
     * Supprime un article et le fichier image qui lui est rattaché : sans ça,
     * le disque se remplit de fichiers que plus rien ne référence.
     */
    public function supprimer(Article $article)
    {
        $image = $article->image;

        $article->delete();

        if ($image) {
            Storage::disk('uploads')->delete($image);
        }

        return response()->json(['message' => 'Article supprimé.']);
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
            'description' => 'nullable|string|max:20000',
            'date' => 'required|date',
            'service_id' => 'required|exists:services,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);
    }
}
