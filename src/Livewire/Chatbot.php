<?php
namespace Djib\AiAgent\Livewire;

use Livewire\Component;
use Djib\AiAgent\Services\TenantAwareResponder;

class Chatbot extends Component
{
    public $message = '';
    public $conversation = [];

    public function send()
    {
        $this->conversation[] = ['user' => $this->message];

        $reply = app(TenantAwareResponder::class)->respond($this->message, auth()->check() ? 'tenant' : 'support');
        $this->conversation[] = ['bot' => $reply];

        $this->message = '';
    }

    public function render()
    {
        return view('ai-agent::livewire.chatbot');
    }
}
