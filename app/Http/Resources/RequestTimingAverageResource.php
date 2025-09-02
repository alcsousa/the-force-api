<?php

namespace App\Http\Resources;

use App\Models\RequestTimingAverage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read RequestTimingAverage $resource
 */
class RequestTimingAverageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'average_duration' => $this->resource->average_duration ?? 0,
        ];
    }
}
