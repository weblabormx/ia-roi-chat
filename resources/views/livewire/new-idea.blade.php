<div class="my-48 max-w-2xl mx-auto text-center bg-gray-700 p-8">
    <form wire:submit="save" class="space-y-4"> 
        <h2 class="text-lg">¿Whats your idea or plan?</h2>
        <x-input label="Name" wire:model="name" />
        <x-input label="Description" placeholder="Tell us about your idea" wire:model="message" />
        <x-button white label="Execute" type="submit" full />
    </form>
    <small class="mt-4 block text-gray-300 text-xs">The generated ROI is an estimate made by AI and may not accurately reflect actual results. We recommend verifying and supplementing it with additional analysis or the help of an expert.</small>
</div>
