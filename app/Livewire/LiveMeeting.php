<?php

namespace App\Livewire;

use App\Models\Idea;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class LiveMeeting extends Component
{
    use WithFileUploads;

    public $idea, $meeting, $message, $audioFile;
    public $rules = [
        'message' => 'required|min:1'
    ];

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
        $this->meeting = $idea->meetings()->where('is_finished', false)->first();
        if(!is_object($this->meeting)) {
            abort(404);
        }
    }

    public function sendMessage()
    {
        $this->validate();
        $this->meeting->messages()->create([
            'message' => $this->message,
            'role' => 'user'
        ]);
        $this->message = '';

        $this->dispatch('messageSent');
    }

    public function saveAudio()
    {
        if (!$this->audioFile) {
            return;
        }

        $path = $this->audioFile->store('audio');

        // Aquí tomamos la clave y el endpoint de las variables de entorno
        $subscriptionKey = env('AZURE_OPENAI_API_KEY');
        $endpoint = env('AZURE_SPEECH_TO_TEXT_ENDPOINT');

        // Abrimos el archivo de audio para enviarlo en el cuerpo de la solicitud
        $real_path = Storage::path($path);
        $audioContent = fopen($real_path, 'r');

        // Enviar la solicitud POST a la API de Azure Speech-to-Text
        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $subscriptionKey,
            'Accept' => 'application/json',
        ])->attach(
            'audio', $audioContent, 'audio.wav'
        )->post($endpoint, [
            'definition' => json_encode([
                'locales' => ['en-US'],
                'profanityFilterMode' => 'Masked',
                'channels' => [0, 1]
            ])
        ]);

        if (!$response->successful()) {
            return;
        }

        $text = collect($response->json()['combinedPhrases'])->pluck('text')->implode(' ');
        $this->message .= $text.' ';
        $this->audioFile = null;
    }

    public function cancel()
    {
        $this->meeting->idea->delete();
        return redirect('dashboard');
    }

    public function render()
    {
        return view('livewire.live-meeting');
    }
}
