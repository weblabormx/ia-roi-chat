<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;
use App\Models\Meeting;

class AzureChat
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = env('AZURE_OPENAI_BASE_URL'); 
        $this->apiKey = env('AZURE_OPENAI_API_KEY'); 
    }

    public function sendMessage(Meeting $meeting)
    {
        // Instrucciones iniciales del sistema
        $messages = [
            [
                'role' => 'system',
                'content' => "Eres un asistente especializado en el cálculo del Retorno de Inversión (ROI). Tu única tarea es hacer preguntas para recopilar información detallada sobre los gastos e ingresos proyectados de un negocio, con el fin de calcular su ROI.

Instrucciones de interacción:
Haz preguntas cortas y concisas una por una, esperando la respuesta del usuario antes de hacer la siguiente.
No respondas preguntas que no tengan que ver con el cálculo del ROI o del proyecto que habla.
Identifica el tipo de negocio y detecta los posibles gastos relacionados con base en la información proporcionada. Por ejemplo:
Si es un negocio físico, pregunta por renta, insumos, permisos, etc.
Si es un negocio en línea, pregunta por hosting, publicidad, plataformas, etc.
Pregunta específicamente sobre empleados:
¿Tendrá empleados?
Si sí, solicita sus nombres, salarios y cualquier otro costo asociado.
Confirma que tienes toda la información antes de finalizar:
Cuando hayas recopilado todo lo necesario, pregunta: 'Tengo toda la información clara. ¿Tienes algo más que agregar?'
Si el usuario responde 'no', responde con la palabra 'TERMINAR' y detén la interacción.
Tu objetivo es obtener una visión completa y clara de los costos, ingresos y riesgos antes de finalizar la conversación."
            ]
        ];

        // Recuperar mensajes previos para mantener el contexto
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

        // Enviar la solicitud a Azure OpenAI
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'api-key' => $this->apiKey,
        ])->post($this->baseUrl, [
            'messages' => $messages,
        ]);

        if (!$response->successful()) {
            $meeting->messages()->create([
                'message' => $response->json()['error']['message'] ?? 'Error desconocido',
                'role' => 'error'
            ]);
            return;
        }
        // Obtener la respuesta de la IA
        $response = $response->json('choices.0.message.content') ?? null;
        $meeting->messages()->create([
            'message' => $response,
            'role' => 'assistant'
        ]);
    }

}
