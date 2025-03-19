<?php

namespace App\Livewire;

use Livewire\Component;

class NewIdea extends Component
{
    public $name, $message;
    public $rules = [
        'name' => 'required',
        'message' => 'required|min:10'
    ];

    public function save()
    {
        $this->validate();
        $idea = auth()->user()->ideas()->create([
            'title' => $this->name
        ]);
        $idea->messages()->create([
            'message' => $this->message,
            'role' => 'user'
        ]);
        return redirect('ideas/'.$idea->id);
    }
    
    public function render()
    {
        return view('livewire.new-idea');
    }
}
