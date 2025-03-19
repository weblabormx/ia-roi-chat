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
    }

    public function sendMessage()
    {
        $this->validate();
        $this->idea->messages()->create([
            'message' => $this->message,
            'role' => 'user'
        ]);
        $this->message = '';
    }

    public function render()
    {
        return view('livewire.see-idea');
    }
}
