<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestTimingAverage extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'average_duration',
    ];
}
