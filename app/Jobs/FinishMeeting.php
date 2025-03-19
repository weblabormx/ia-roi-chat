<?php

namespace App\Jobs;

use App\Classes\AzureChat;
use App\Models\Meeting;
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
            'content' => "De toda la conversación genera un json con la siguiente información (json puro, no markdown):
                - title - Propuesta de titulo de lo discutido en la conversación
                - resume - Resumen de la conversación con toda la información relevante, no quiero que nada se pierda, necesito que toda la información para calular 
                    ROI esté presente y necesito que el resumen sea lo más completo posible y en lenguaje natural
                - description - Una descripción corta de 100 caracteres máximo"
        ];

        $response = $azure->callApi($messages);
        $response = $response->json('choices.0.message.content') ?? null;
        $data = json_decode($response, true);
        $this->meeting->update($data);
    }
}
