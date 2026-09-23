<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Message;
use App\Models\Realisation;
use App\Models\Service;

class DashboardController extends Controller
{
    /** Nombre de demandes récentes remontées sur le tableau de bord. */
    private const DERNIERS_MESSAGES = 5;

    /**
     * Chiffres de l'activité du site et dernières demandes reçues.
     *
     * Rien n'est stocké dans la table `dashboards` : un tableau de bord se
     * recalcule à chaque affichage, sinon il affiche des chiffres périmés.
     * Ce sont des COUNT en SQL, pas des collections chargées puis comptées
     * côté PHP — on ne rapatrie jamais les lignes pour les dénombrer.
     */
    public function index()
    {
        $messagesNonTraites = Message::where('traite', false)->count();

        return response()->json([
            'compteurs' => [
                'messages' => [
                    'total' => Message::count(),
                    'nouveaux' => $messagesNonTraites,
                ],
                'articles' => [
                    'total' => Article::count(),
                ],
                'realisations' => [
                    'total' => Realisation::count(),
                ],
                'metiers' => [
                    'total' => Service::count(),
                    'actifs' => Service::visibles()->count(),
                ],
            ],

            // Les non traitées d'abord : c'est ce sur quoi l'artisan doit agir.
            // À défaut, les plus récentes, pour ne pas afficher un bloc vide.
            'derniersMessages' => Message::query()
                ->with('service:id,nom,slug,couleur,icone')
                ->orderBy('traite')
                ->orderByDesc('created_at')
                ->limit(self::DERNIERS_MESSAGES)
                ->get(),
        ]);
    }
}
