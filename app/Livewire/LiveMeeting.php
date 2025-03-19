<?php

namespace App\Livewire;

use App\Models\Idea;
use Livewire\Component;

class LiveMeeting extends Component
{
    public $idea, $meeting, $message;
    public $rules = [
        'message' => 'required|min:4'
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

    public function render()
    {
        return view('livewire.live-meeting');
    }
}
