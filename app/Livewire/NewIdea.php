<?php

namespace App\Livewire;

use App\Classes\AzureLanguage;
use Livewire\Component;

class NewIdea extends Component
{
    public $name, $message;
    public $rules = [
        'name' => 'required',
        'message' => 'required|min:20'
    ];

    public function save()
    {
        $this->validate();
        $azure = new AzureLanguage;
        $lang = $azure->detectLanguage($this->message);
        $idea = auth()->user()->ideas()->create([
            'title' => $this->name,
            'language' => $lang
        ]);
        $meeting = $idea->meetings()->create();
        $meeting->messages()->create([
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
