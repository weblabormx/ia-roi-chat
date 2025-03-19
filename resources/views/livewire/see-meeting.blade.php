<div class="max-w-5xl mx-auto relative h-full">
    <div class="space-y-4 pr-4" >
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
</div>