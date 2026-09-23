<?php

namespace App\Http\Controllers;

use App\Mail\NouvelleDemande;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    /** Nombre de demandes affichées par page dans l'admin. */
    private const PAR_PAGE = 10;

    public function envoyerMessage(Request $request)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'required|string|max:20',
            'message' => 'required|string',
            'service_id' => 'required|exists:services,id',
        ]);

        $message = Message::create($validatedData);

        $this->notifierArtisan($message);

        return response()->json(['message' => 'Message envoyé avec succès', 'data' => $message], 201);
    }

    /**
     * Prévient l'artisan par email qu'une demande vient d'arriver.
     *
     * L'échec d'envoi est journalisé mais jamais remonté au visiteur : sa
     * demande est déjà enregistrée en base, elle n'est pas perdue. Lui
     * renvoyer une erreur le pousserait à renvoyer le formulaire et à créer
     * des doublons, pour un problème qui ne le concerne pas.
     */
    private function notifierArtisan(Message $message): void
    {
        $destinataire = config('mail.artisan');

        if (blank($destinataire)) {
            Log::warning('MAIL_ARTISAN non configuré : notification non envoyée.', [
                'message_id' => $message->id,
            ]);

            return;
        }

        try {
            Mail::to($destinataire)->send(
                new NouvelleDemande($message->load('service:id,nom'))
            );
        } catch (\Throwable $e) {
            Log::error('Notification de nouvelle demande non envoyée.', [
                'message_id' => $message->id,
                'erreur' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Liste paginée des demandes, les plus récentes d'abord.
     *
     * Le tri et le filtrage sont faits en SQL, pas côté navigateur : avec la
     * pagination, filtrer après coup ne porterait que sur la page affichée.
     *
     * `statut` : "nouveaux", "traites", ou rien pour tout.
     */
    public function listerMessages(Request $request)
    {
        $request->validate([
            'statut' => 'nullable|in:nouveaux,traites',
            'page' => 'nullable|integer|min:1',
        ]);

        $messages = Message::query()
            // Colonnes d'affichage seulement : inutile de charger les
            // prestations du métier pour une carte de message.
            ->with('service:id,nom,slug,couleur,icone')
            ->when($request->statut === 'nouveaux', fn ($q) => $q->where('traite', false))
            ->when($request->statut === 'traites', fn ($q) => $q->where('traite', true))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(self::PAR_PAGE);



        // Les compteurs portent sur la totalité, pas sur la page courante :
        // ils alimentent le sélecteur de filtre.
        return response()->json([
            'donnees' => $messages->items(),
            'page' => $messages->currentPage(),
            'dernierePage' => $messages->lastPage(),
            'total' => $messages->total(),
            'compteurs' => [
                'tous' => Message::count(),
                'nouveaux' => Message::where('traite', false)->count(),
                'traites' => Message::where('traite', true)->count(),
            ],
        ]);
    }

    /**
     * Bascule l'état d'une demande : traitée si elle ne l'est pas, et
     * inversement. Retourne le message à jour pour que l'admin reflète
     * l'état réel en base plutôt que de le supposer.
     */
    public function traiter(Message $message)
    {
        $message->update(['traite' => ! $message->traite]);

        return response()->json($message->load('service:id,nom,slug,couleur,icone'));
    }

    public function supprimer(Message $message)
    {
        $message->delete();

        return response()->json(['message' => 'Message supprimé.']);
    }
}
