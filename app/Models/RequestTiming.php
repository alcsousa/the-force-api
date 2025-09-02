<?php

namespace App\Models;

use Database\Factories\RequestTimingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestTiming extends Model
{
    /** @use HasFactory<RequestTimingFactory> */
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'duration',
        'created_at',
    ];
}
