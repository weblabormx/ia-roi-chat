<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;
use App\Models\Idea;

class AzureChat
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = env('AZURE_OPENAI_BASE_URL'); 
        $this->apiKey = env('AZURE_OPENAI_API_KEY'); 
    }


    public function createThread(Idea $idea)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/threads", []);

        if ($response->successful()) {
            $data = $response->json();
            $idea->thread_id = $data['id'];
            $idea->save();
            return $idea->thread_id;
        }

        return null;
    }

    public function sendMessage(Idea $idea, string $message)
    {
        if (!$idea->thread_id) {
            $this->createThread($idea);
        }

        Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/threads/{$idea->thread_id}/messages", [
            'role' => 'user',
            'content' => $message,
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/threads/{$idea->thread_id}/run", [
            'model' => 'gpt-4', 
        ]);

        if ($response->successful()) {
            return $response->json()['choices'][0]['message']['content'] ?? 'No response';
        }

        return 'Error al procesar la solicitud.';
    }
}
