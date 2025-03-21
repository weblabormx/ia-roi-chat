<?php

namespace App\Jobs;

use App\Classes\AzureChat;
use App\Models\Idea;
use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class GenerateGraphics implements ShouldQueue
{
    use Queueable;

    public function __construct(public Idea $idea) {}

    public function handle(): void
    {
        $azure = new AzureChat('DeepSeek-V3');

        $messages = [
            [
                'role' => 'system',
                'content' => Setting::getColumn('graphics_prompt')
            ],
            [
                'role' => 'user',
                'content' => $this->idea->analysis
            ],
        ];

        $response = $azure->callApi($messages);
        $response = $response->json('choices.0.message.content') ?? null;
        $response = str_replace('```html', '', $response);
        $response = str_replace('```', '', $response);

        // Save response on html on Storage
        $path = 'graphics/'.$this->idea->id.'.html';
        Storage::put($path, $response);

    }
}
