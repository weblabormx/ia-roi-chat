<?php

namespace App\Jobs;

use App\Classes\AzureChat;
use App\Models\Idea;
use Illuminate\Contracts\Broadcasting\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendMessage implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct(public Idea $idea) {}

    public function handle(): void
    {
        $azureChat = new AzureChat();
        $azureChat->sendMessage($this->idea);
    }

    public function uniqueId(): string
    {
        return $this->idea->id;
    }
}
