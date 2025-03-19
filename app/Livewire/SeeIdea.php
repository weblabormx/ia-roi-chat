<?php

namespace App\Livewire;

use App\Models\Idea;
use Livewire\Attributes\On;
use Livewire\Component;

class SeeIdea extends Component
{
    protected $listeners = ['refreshComponent' => '$refresh'];
    
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

        $this->dispatch('messageSent');
    }

    public function render()
    {
        return view('livewire.see-idea');
    }
}
