<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdeaMessage extends Model
{
    protected $guarded = [];

    /**
     * Relationships
     */

    public function idea()
    {
        return $this->belongsTo(Idea::class);
    }
}
