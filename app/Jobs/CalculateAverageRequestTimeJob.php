<?php

namespace App\Jobs;

use App\Models\RequestTiming;
use App\Models\RequestTimingAverage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class CalculateAverageRequestTimeJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public function handle(): void
    {
        $average = RequestTiming::where('created_at', '>=', now()->subMinutes(5))->avg('duration');

        RequestTimingAverage::query()->create([
            'average_duration' => $average ?? 0,
        ]);
    }
}
