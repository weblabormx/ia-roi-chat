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

    public function sendMessage(Idea $idea)
    {
        // Instrucciones iniciales del sistema
        $messages = [
            [
                'role' => 'system',
                'content' => "Vas a tomar el rol de Asesor/consultor para emprededores, pequeñas y medianas empresas. Aqui hablarás sobre el proyecto '{$idea->title}'. 
                    Tu labor es preguntar los datos necesarios para poder evaluar los riesgos de inversion de una calculadora ROI. Haras una pregunta y esperaras respuesta del usuario 
                    hasta terminar con la informacion necesaria. Esos datos los deberas guardar para despues generar una respuesta en tablas y graficas con riesgos minimos y riesgos 
                    maximos. Si te preguntan de otra cosa que no sea tu rol e,viaras el mensaje de que nada mas estas calificado para ayudar en esta labor. No me muestres lo capturado en 
                    pantalla por el usuario, al final comentame el resumen de lo guardado. Genera un JSON con la informacion capturada"
            ]
        ];

        // Recuperar mensajes previos para mantener el contexto
        $messages = array_merge($messages, $idea->messages()
            ->orderBy('created_at')
            ->whereNotIn('role', ['error'])
            ->get()
            ->map(fn($msg) => [
                'role' => $msg->role,
                'content' => $msg->message
            ])
            ->toArray()
        );

        // Enviar la solicitud a Azure OpenAI
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'api-key' => $this->apiKey,
        ])->post($this->baseUrl, [
            'messages' => $messages,
        ]);

        if (!$response->successful()) {
            $idea->messages()->create([
                'message' => $response->json()['error']['message'] ?? 'Error desconocido',
                'role' => 'error'
            ]);
            return;
        }
        // Obtener la respuesta de la IA
        $response = $response->json('choices.0.message.content') ?? null;
        $idea->messages()->create([
            'message' => $response,
            'role' => 'assistant'
        ]);
    }

}
