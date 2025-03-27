<div class="my-12 max-w-2xl mx-auto text-center dark:bg-gray-700 p-8">
    <form wire:submit="save" class="space-y-4"> 
        <h2 class="text-lg">Prompts</h2>
        <x-textarea label="Chat" wire:model="settings.chat_prompt" />
        <x-textarea label="Meeting Resume" wire:model="settings.meeting_prompt" />
        <x-textarea label="Analysis" wire:model="settings.analysis_prompt" />
        <x-textarea label="Graphics" wire:model="settings.graphics_prompt" />
        <x-button gray label="Update" type="submit" full />
    </form>
</div>
