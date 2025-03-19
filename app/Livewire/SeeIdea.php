<?php

namespace App\Livewire;

use App\Models\Idea;
use Livewire\Component;

class SeeIdea extends Component
{
    public $idea;

    public function mount(Idea $idea)
    {
        $this->idea = $idea;
    }
    
    public function render()
    {
        return view('livewire.see-idea');
    }
}
