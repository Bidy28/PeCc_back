<x-mail::message>
# Nouvelle demande de devis

**{{ $demande->nom }}** vient de remplir la demande de devis du site.

<x-mail::panel>
**Métier concerné :** {{ $demande->service?->nom ?? 'Non précisé' }}
**Téléphone :** {{ $demande->telephone }}
**Email :** {{ $demande->email }}
**Reçue le :** {{ $demande->created_at->timezone('Europe/Paris')->format('d/m/Y à H:i') }}
</x-mail::panel>

## Son message

{{-- nl2br conserve les retours à la ligne saisis par le visiteur ; e() échappe
     le contenu avant, sinon un message contenant du HTML serait interprété. --}}
{!! nl2br(e($demande->message)) !!}

<x-mail::button :url="'tel:'.preg_replace('/\s+/', '', $demande->telephone)">
Appeler {{ $demande->nom }}
</x-mail::button>
{{--
Vous pouvez aussi répondre directement à cet email : votre réponse arrivera
dans la boîte de {{ $demande->nom }}. --}}

Merci,<br>
{{ config('app.name') }}
</x-mail::message>
