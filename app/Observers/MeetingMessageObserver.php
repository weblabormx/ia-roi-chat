<?php

namespace App\Observers;

use App\Jobs\SendMessage;
use App\Models\MeetingMessage;

class MeetingMessageObserver
{
    public function created(MeetingMessage $meeting_message)
    {
        if($meeting_message->role == 'user') {
            SendMessage::dispatch($meeting_message->meeting);
        } else if($meeting_message->role == 'assistant' && $meeting_message->message == 'TERMINAR') {
            $meeting_message->meeting->update(['is_finished' => true]);
        }
    }
}
