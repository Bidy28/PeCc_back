<?php

namespace App\Mail;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NouvelleDemande extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Message $demande)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouveau message de contact — '.$this->demande->nom,

            // Répondre à l'email part directement chez le client final.
            // L'expéditeur reste l'adresse du site : mettre celle du visiteur
            // ferait passer le message pour une usurpation (SPF/DKIM) et
            // l'enverrait en indésirables.
            replyTo: [new Address($this->demande->email, $this->demande->nom)],
        );
    }

    public function content(): Content
    {
        // markdown: et non view: — c'est ce qui charge les composants
        // <x-mail::...> et le gabarit HTML de Laravel.
        return new Content(markdown: 'emails.nouvelle-demande');
    }
}
