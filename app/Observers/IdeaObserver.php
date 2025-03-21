<?php

namespace App\Observers;

use App\Classes\AzureChat;
use App\Models\Idea;

class IdeaObserver
{
    public function deleting(Idea $idea)
    {
        $idea->meetings()->delete();
    }
}
