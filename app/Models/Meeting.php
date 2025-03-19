<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
