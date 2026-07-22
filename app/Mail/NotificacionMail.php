<?php

namespace App\Mail;

use App\Models\Notificacion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class NotificacionMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Notificacion $notificacion
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('no-reply@limpiaturincon.ec', 'LimpiaTuRincón'),
            subject: 'Notificación municipal — Ordenanza 332'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notificacion',
            with: [
                'notificacion' => $this->notificacion,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $adjuntos = [];

        // Verificamos si existe la relación con la denuncia y si tiene un archivo guardado
        if ($this->notificacion->denuncia && !empty($this->notificacion->denuncia->foto_path)) {
            $rutaFoto = $this->notificacion->denuncia->foto_path;

            // Comprobamos que el archivo exista físicamente en el disco configurado (por ejemplo, 'public')
            if (Storage::disk('public')->exists($rutaFoto)) {
                $adjuntos[] = Attachment::fromStorageDisk('public', $rutaFoto)
                    ->as('Evidencia_Denuncia_' . $this->notificacion->denuncia_id . '.jpg')
                    ->withMime('image/jpeg');
            }
        }

        return $adjuntos;
    }
}
