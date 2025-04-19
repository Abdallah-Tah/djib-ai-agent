<div class="flex flex-col h-full">
    <div class="flex-1 p-4 space-y-4 overflow-y-auto">
        @if (empty($conversation))
            <div class="my-8 text-center text-gray-500 dark:text-gray-400">
                Start a conversation by typing a message below
            </div>
        @else
            @foreach ($conversation as $message)
                @if (isset($message['user']))
                    <div class="flex justify-end">
                        <div class="user-message bg-blue-600 text-white rounded-lg py-2 px-4 max-w-[80%]">
                            {{ $message['user'] }}
                        </div>
                    </div>
                @endif
                @if (isset($message['bot']))
                    <div class="flex justify-start">
                        <div class="bot-message bg-gray-100 dark:bg-gray-700 rounded-lg py-2 px-4 max-w-[80%]">
                            {{ $message['bot'] }}
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    </div>

    <div class="p-4 bg-white border-t dark:border-gray-700 dark:bg-gray-800">
        <form wire:submit.prevent="send" class="flex gap-2">
            <input type="text" wire:model="message" placeholder="Type your message..."
                class="flex-1 px-4 py-2 bg-white border border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-white">
            <button type="submit"
                class="px-4 py-2 text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                Send
            </button>
        </form>
    </div>
</div>
