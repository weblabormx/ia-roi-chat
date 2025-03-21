<?php

namespace App\Models;

use App\Observers\IdeaObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[ObservedBy(IdeaObserver::class)]
class Idea extends Model
{
    protected $guarded = [];

    /**
     * Relationships
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    /*
     * Attributes
     */

    public function getGraphicsAttribute()
    {
        return Storage::get('graphics/'.$this->id.'.html');
    }
}
