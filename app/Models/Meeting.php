<?php

namespace App\Models;

use App\Observers\MeetingObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(MeetingObserver::class)]
class Meeting extends Model
{
    protected $guarded = [];

    /**
     * Relationships
     */

    public function idea()
    {
        return $this->belongsTo(Idea::class);
    }

    public function messages()
    {
        return $this->hasMany(MeetingMessage::class);
    }
}
