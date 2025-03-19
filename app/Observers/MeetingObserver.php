<?php

namespace App\Observers;

use App\Jobs\FinishMeeting;
use App\Models\Meeting;

class MeetingObserver
{
    public function updating(Meeting $meeting)
    {
        if($meeting->is_finished && is_null($meeting->resume)) {
            FinishMeeting::dispatch($meeting);
        }
    }
}
