<?php

namespace App\Http\Controllers;

use App\Http\Resources\RequestTimingAverageResource;
use App\Models\RequestTimingAverage;

class MetricsController extends Controller
{
    public function __invoke(): RequestTimingAverageResource
    {
        return RequestTimingAverageResource::make(
            RequestTimingAverage::query()->latest()->first()
        );
    }
}
