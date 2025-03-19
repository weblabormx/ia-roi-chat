<div class="max-w-5xl mx-auto relative h-full">
    <div class="space-y-4">
        @foreach($idea->messages()->with(['idea', 'idea.user'])->get() as $message)
            @if($message->role == 'user')
                <div class="bg-gray-700 p-4 my-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ $message->idea->user->initials() }}
                                </span>
                            </span>
                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">{{ $message->idea->user->name }}</span>
                                <span class="truncate text-xs">{{ $message->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p>{{ $message->message }}</p>
                    </div>
                </div>
            @else
                <div>{{ $message->message }}</div>
            @endif
        @endforeach
    </div>
    <div class="bg-gray-700 p-8 absolute" style="bottom: 0; left:0; right: 0">
        <form wire:submit="sendMessage" class="space-y-4"> 
            <x-textarea label="Message" placeholder="Tell us more information" wire:model="message" />
            <x-button white label="Send Message" type="submit" full />
        </form>
    </div>
</div>
