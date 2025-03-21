<?php

namespace App\Livewire;

use App\Jobs\FinishMeeting;
use App\Models\Meeting;
use Livewire\Component;

class SeeMeeting extends Component
{
    public $meeting;

    public function mount(Meeting $meeting)
    {
        $this->meeting = $meeting;

        if(request()->filled('regenerate')) {
            FinishMeeting::dispatch($meeting);
            return redirect('meetings/'.$meeting->id);
        }
    }

    public function render()
    {
        return view('livewire.see-meeting');
    }
}
