<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UpdateClientMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $toEmail,
        public ?string $toName,
        public string $subject,
    ) {}

    public function build()
    {
        return $this->subject('Your message was sent successfully')
            ->view('emails.update')
            ->with([
                'toEmail' => $this->toEmail,
                'toName' => $this->toName,
                'originalSubject' => $this->subject,
            ]);
    }
}


