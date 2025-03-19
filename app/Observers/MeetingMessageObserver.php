<?php

namespace App\Observers;

use App\Jobs\SendMessage;
use App\Models\MeetingMessage;

class MeetingMessageObserver
{
    public function created(MeetingMessage $meeting_message)
    {
        if($meeting_message->role == 'user') {
            SendMessage::dispatch($meeting_message->idea);
        }
    }
}
