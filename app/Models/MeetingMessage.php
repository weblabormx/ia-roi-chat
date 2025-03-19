<?php

namespace App\Models;

use App\Observers\MeetingMessageObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(MeetingMessageObserver::class)]
class MeetingMessage extends Model
{
    protected $guarded = [];

    /**
     * Relationships
     */

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
}
