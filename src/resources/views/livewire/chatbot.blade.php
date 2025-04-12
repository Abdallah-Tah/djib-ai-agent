<div class="max-w-xl p-4 mx-auto bg-white border rounded shadow">
    <div class="h-64 mb-4 space-y-2 overflow-y-auto">
        @foreach ($conversation as $entry)
            <div><strong>User:</strong> {{ $entry['user'] ?? '' }}</div>
            <div class="text-gray-700"><strong>Bot:</strong> {{ $entry['bot'] ?? '' }}</div>
        @endforeach
    </div>

    <input type="text" wire:model.defer="message" wire:keydown.enter="send" class="w-full p-2 border rounded shadow-sm"
        placeholder="Type your message...">

    <button wire:click="send" class="px-4 py-2 mt-2 text-white bg-blue-600 rounded hover:bg-blue-700">
        Send
    </button>
</div>
<script>
    window.addEventListener('message-sent', event => {
        const chatContainer = document.querySelector('.overflow-y-auto');
        chatContainer.scrollTop = chatContainer.scrollHeight;
    });
</script>
