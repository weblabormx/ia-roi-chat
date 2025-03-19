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
        if($idea->meetings()->where('is_finished', true)->count() == 0) {
            return redirect('ideas/'.$idea->id.'/live_meeting');
        }
    }

    public function newMeeting()
    {
        $this->idea->meetings()->create([
            'is_finished' => false
        ]);
        return redirect('ideas/'.$this->idea->id.'/live_meeting');
    }

    public function render()
    {
        return view('livewire.see-idea');
    }
}
