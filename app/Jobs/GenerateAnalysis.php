<?php

namespace App\Jobs;

use App\Classes\AzureChat;
use App\Models\Idea;
use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateAnalysis implements ShouldQueue
{
    use Queueable;

    public function __construct(public Idea $idea) {}

    public function handle(): void
    {
        $azure = new AzureChat;

        $messages = [
            [
                'role' => 'system',
                'content' => Setting::getColumn('analysis_prompt')
            ]
        ];

        $messages = array_merge($messages, $this->idea->meetings()
            ->orderBy('created_at')
            ->get()
            ->map(fn($idea) => [
                'role' => 'user',
                'content' => $idea->resume
            ])
            ->toArray()
        );

        $response = $azure->callApi($messages);
        $response = $response->json('choices.0.message.content') ?? null;
        $this->idea->update(['analysis' => $response]);

        GenerateGraphics::dispatch($this->idea);
    }
}
