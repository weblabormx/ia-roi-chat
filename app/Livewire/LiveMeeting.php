<?php

namespace App\Livewire;

use App\Models\Idea;
use Livewire\Component;
use Livewire\WithFileUploads;

class LiveMeeting extends Component
{
    use WithFileUploads;

    public $idea, $meeting, $message, $audioFile;
    public $rules = [
        'message' => 'required|min:2'
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

        $path = $this->audioFile->store('public/audio');
        dd($path);
        $this->audioFile = null;
    }


    public function render()
    {
        return view('livewire.live-meeting');
    }
}
