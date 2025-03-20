<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    /**
     * Functions
     */

    public static function getColumn($column)
    {
        return self::where('column', $column)->first()->value;
    }
}
