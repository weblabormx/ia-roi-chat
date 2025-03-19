<div class="max-w-5xl mx-auto relative h-full">
    <div id="messagesContainer" class="space-y-4 overflow-y-auto overflow-x-hidden pr-4" style="height: calc(100vh - 280px)" wire:poll>
        @foreach($meeting->messages()->get() as $message)
            @if($message->role == 'user')
                <div class="bg-gray-500 p-4 my-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ $meeting->idea->user->initials() }}
                                </span>
                            </span>
                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">{{ $meeting->idea->user->name }}</span>
                                <span class="truncate text-xs">{{ $message->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 space-y-4">
                        {!! Str::markdown($message->message) !!}
                    </div>
                </div>
            @elseif($message->role == 'error')
                <div class="text-red-300 text-sm">
                    {{ $message->message }}
                </div>
            @else
                <div class="space-y-4">
                    {!! Str::markdown($message->message) !!}
                </div>
            @endif
        @endforeach
    </div>
    @if(!$meeting->is_finished)
        <div class="bg-gray-700 p-8 absolute" style="bottom: 0; left:0; right: 0">
            <form wire:submit="sendMessage" class="space-y-4"> 
                <x-input label="Message" placeholder="Tell us more information" wire:model="message" />
                <x-button white label="Send Message" type="submit" full />
            </form>
        </div>
    @else
        <div class="bg-gray-700 p-8 absolute" style="bottom: 0; left:0; right: 0">
            <x-button white label="Finish Meeting" href="/ideas/{{$meeting->id}}" full />
        </div>
    @endif
</div>

<script>
    function scrollToBottom() {
        let container = document.getElementById('messagesContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        scrollToBottom();
    });
</script>
@script
    <script>
        function scrollToBottom() {
            let container = document.getElementById('messagesContainer');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }
        Livewire.hook('request', ({ component, cleanup }) => {
            scrollToBottom();
        });
       
    </script>
@endscript