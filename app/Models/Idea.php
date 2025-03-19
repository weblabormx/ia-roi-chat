<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Idea extends Model
{
    protected $guarded = [];

    /**
     * Relationships
     */

    public function messages()
    {
        return $this->hasMany(IdeaMessage::class);
    }
}
