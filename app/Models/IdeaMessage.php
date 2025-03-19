<?php

namespace App\Models;

use App\Observers\IdeaMessageObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(IdeaMessageObserver::class)]
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
