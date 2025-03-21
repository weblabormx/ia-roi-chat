<?php

namespace App\Livewire;

use App\Models\Meeting;
use Livewire\Component;

class SeeMeeting extends Component
{
    public $meeting;

    public function mount(Meeting $meeting)
    {
        $this->meeting = $meeting;
    }

    public function render()
    {
        return view('livewire.see-meeting');
    }
}
