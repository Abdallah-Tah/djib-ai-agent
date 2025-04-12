<?php

namespace Djib\AiAgent\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\EscalationAlert;

class EscalationService
{
    public function escalate(string $message, string $from = 'Anonymous User')
    {
        foreach (config('ai-agent.operators') as $operator) {
            match ($operator['method']) {
                'email' => $this->sendEmail($operator['destination'], $message, $from),
                'whatsapp' => $this->sendWhatsApp($operator['destination'], $message, $from),
                'sms' => $this->sendSms($operator['destination'], $message, $from),
                'telegram' => $this->sendTelegram($operator['destination'], $message, $from),
                default => null,
            };
        }
    }

    protected function sendEmail($to, $message, $from)
    {
        Mail::to($to)->send(new EscalationAlert($from, $message));
    }

    protected function sendWhatsApp($to, $message, $from)
    {
        Http::withToken(config('services.whatsapp.token'))->post(
            'https://graph.facebook.com/v18.0/' . config('services.whatsapp.phone_id') . '/messages',
            [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'text',
                'text' => ['body' => "👤 {$from} needs help:\n{$message}"]
            ]
        );
    }

    protected function sendSms($to, $message, $from)
    {
        Http::withBasicAuth(config('services.twilio.sid'), config('services.twilio.token'))->post(
            'https://api.twilio.com/2010-04-01/Accounts/' . config('services.twilio.sid') . '/Messages.json',
            [
                'To' => $to,
                'From' => config('services.twilio.from'),
                'Body' => "{$from} triggered chatbot escalation:\n{$message}"
            ]
        );
    }

    protected function sendTelegram($to, $message, $from)
    {
        Http::post('https://api.telegram.org/bot' . config('services.telegram.bot_token') . '/sendMessage', [
            'chat_id' => $to,
            'text' => "📢 {$from} asked for help:\n{$message}"
        ]);
    }
}
