<div>
    <div class="chat-messages">
        @foreach ($conversation as $message)
            @if (isset($message['user']))
                <div class="user-message">
                    {{ $message['user'] }}
                </div>
            @endif
            @if (isset($message['bot']))
                <div class="bot-message">
                    {{ $message['bot'] }}
                </div>
            @endif
        @endforeach
    </div>

    <form wire:submit.prevent="send">
        <input type="text" wire:model="message" placeholder="Type your message...">
        <button type="submit">Send</button>
    </form>
</div>
