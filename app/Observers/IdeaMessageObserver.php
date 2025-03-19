<?php

namespace App\Observers;

use App\Jobs\SendMessage;
use App\Models\IdeaMessage;

class IdeaMessageObserver
{
    public function created(IdeaMessage $ideaMessage)
    {
        if($ideaMessage->role == 'user') {
            SendMessage::dispatch($ideaMessage->idea);
        }
    }
}
