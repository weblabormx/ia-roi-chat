<?php

namespace App\Jobs;

use App\Classes\AzureChat;
use App\Models\Meeting;
use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FinishMeeting implements ShouldQueue
{
    use Queueable;

    public function __construct(public Meeting $meeting) {}

    public function handle(): void
    {
        $azure = new AzureChat;

        $messages = $this->meeting->messages()
            ->orderBy('created_at')
            ->whereNotIn('role', ['error'])
            ->get()
            ->map(fn($msg) => [
                'role' => $msg->role,
                'content' => $msg->message
            ])
            ->toArray();
        $messages[] = [
            'role' => 'system',
            'content' => Setting::getColumn('meeting_prompt')
        ];

        $response = $azure->callApi($messages);
        $response = $response->json('choices.0.message.content') ?? null;
        $data = json_decode($response, true);
        $this->meeting->update($data);

        GenerateAnalysis::dispatch($this->meeting->idea);
    }
}
