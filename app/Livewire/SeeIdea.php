<?php

namespace App\Livewire;

use App\Models\Idea;
use Livewire\Component;

class SeeIdea extends Component
{
    public $idea, $message;
    public $rules = [
        'message' => 'required|min:4'
    ];

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
        if($idea->meetings()->where('is_finished', true)->count() == 0) {
            return redirect('ideas/'.$idea->id.'/live_meeting');
        }
    }

    public function sendMessage()
    {
        $this->validate();
        $this->idea->messages()->create([
            'message' => $this->message,
            'role' => 'user'
        ]);
        $this->message = '';

        $this->dispatch('messageSent');
    }

    public function render()
    {
        return view('livewire.see-idea');
    }
}
