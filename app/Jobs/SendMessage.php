<?php

namespace App\Jobs;

use App\Classes\AzureChat;
use App\Models\IdeaMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendMessage implements ShouldQueue
{
    use Queueable;

    public function __construct(public IdeaMessage $message) {}

    public function handle(): void
    {
        $azureChat = new AzureChat();
        $response = $azureChat->sendMessage($this->message->idea, $this->message->message);
        $this->message->idea->create([
            'message' => $response,
            'sent_by_user' => false
        ]);
    }
}
