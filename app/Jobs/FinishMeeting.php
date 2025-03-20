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
            'content' => "De toda la conversación aportada por el usuario en esta sesión (excluyendo la información anterior y evitando repetir datos previos), genera un JSON con la siguiente información (JSON puro, sin markdown):

title: Un título representativo que describa brevemente el tema discutido en esta sesión.
resume: Un resumen detallado únicamente con la nueva información proporcionada en esta sesión. No incluyas información repetida de reuniones anteriores, solo los cambios, decisiones o actualizaciones realizadas por el usuario en esta ocasión. Si solo se hicieron pequeños cambios, el resumen debe reflejar únicamente esos cambios sin extenderse innecesariamente.
description: Una descripción corta de 100 caracteres máximo que sintetice lo más importante de esta sesión."
        ];

        $response = $azure->callApi($messages);
        $response = $response->json('choices.0.message.content') ?? null;
        $data = json_decode($response, true);
        $this->meeting->update($data);

        GenerateAnalysis::dispatch($this->meeting->idea);
    }
}
