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
        dd($response->body());

        if ($response->successful()) {
            return $response->json()['id'];
        }

        return null;
    }

    public function sendMessage(Idea $idea, string $message, string $role = 'user')
    {
        if (!$idea->thread_id) {
            return 'Thread no encontrado.';
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/threads/{$idea->thread_id}/messages", [
            'role' => $role,
            'content' => $message,
        ]);

        if (!$response->successful()) {
            return 'Error al enviar el mensaje.';
        }
    
        if ($role === 'user') {
            return $this->getAIResponse($idea);
        }
    
        return null;
    }

    public function getAIResponse(Idea $idea)
    {
        if (!$idea->thread_id) {
            return 'Thread no encontrado.';
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/threads/{$idea->thread_id}/run", [
            'model' => 'gpt-4',
        ]);

        if ($response->successful()) {
            return $response->json()['choices'][0]['message']['content'] ?? 'No response';
        }

        return 'Error en la respuesta de la IA.';
    }

}
