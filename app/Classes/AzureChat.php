<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;
use App\Models\Meeting;
use App\Models\Setting;

class AzureChat
{
    protected string $baseUrl, $model;
    protected string $apiKey;

    public function __construct($model = "OpenAI")
    {
        $this->model = $model;
        if($model == "OpenAI") {
            $this->baseUrl = env('AZURE_OPENAI_BASE_URL'); 
            $this->apiKey = env('AZURE_OPENAI_API_KEY'); 
        } else {
            $this->baseUrl = env('DEEP_SEEK_BASE_URL');
            $this->apiKey = env('DEEP_SEEK_API_KEY');
        }
    }

    public function sendMessage(Meeting $meeting)
    {
        // Instrucciones iniciales del sistema
        $messages = [
            [
                'role' => 'system',
                'content' => Setting::getColumn('chat_prompt')."\nRegresa el mensaje en el idioma ".$meeting->idea->language
            ]
        ];

        $meetings = $meeting->idea->meetings()->where('id', '!=', $meeting->id)->oldest()->get();
        $messages = array_merge($messages, $meetings
            ->map(fn($meet) => [
                'role' => 'system',
                'content' => "Resume of meeting from {$meet->created_at}: ".$meet->resume
            ])
            ->toArray()
        );

        $messages = array_merge($messages, $meeting->messages()
            ->orderBy('created_at')
            ->whereNotIn('role', ['error'])
            ->get()
            ->map(fn($msg) => [
                'role' => $msg->role,
                'content' => $msg->message
            ])
            ->toArray()
        );

        $response = $this->callApi($messages);
        if (!$response->successful()) {
            $meeting->messages()->create([
                'message' => $response->json()['error']['message'] ?? 'Error desconocido',
                'role' => 'error'
            ]);
            return;
        }

        $response = $response->json('choices.0.message.content') ?? null;
        $meeting->messages()->create([
            'message' => $response,
            'role' => 'assistant'
        ]);
    }

    public function callApi($messages)
    {
        return Http::timeout(600)->withHeaders([
            'Content-Type' => 'application/json',
            'api-key' => $this->apiKey,
        ])->post($this->baseUrl, [
            'messages' => $messages,
            'model' => $this->model
        ]);
    }
}
