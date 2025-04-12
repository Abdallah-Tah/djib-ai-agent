<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EscalationAlert extends Mailable
{
    use Queueable, SerializesModels;

    public string $fromName;
    public string $userMessage;

    public function __construct(string $fromName, string $userMessage)
    {
        $this->fromName = $fromName;
        $this->userMessage = $userMessage;
    }

    public function build(): self
    {
        return $this->subject('🚨 Djib-Agent Escalation Alert')
            ->view('emails.escalation')
            ->with([
                'fromName' => $this->fromName,
                'userMessage' => $this->userMessage,
            ]);
    }
}
