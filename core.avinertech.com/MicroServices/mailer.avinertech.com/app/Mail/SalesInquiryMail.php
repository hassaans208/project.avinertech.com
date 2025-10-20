<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SalesInquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $toEmail,
        public ?string $toName,
        public string $fromEmail,
        public ?string $fromName,
        public string $subject,
        public string $content
    ) {}

    public function build()
    {
        return $this->subject($this->subject)
            ->from($this->fromEmail, $this->fromName ?? null)
            ->view('emails.sales')
            ->with([
                'toEmail' => $this->toEmail,
                'toName' => $this->toName,
                'fromEmail' => $this->fromEmail,
                'fromName' => $this->fromName,
                'subjectLine' => $this->subject,
                'messageContent' => $this->content,
            ]);
    }
}


