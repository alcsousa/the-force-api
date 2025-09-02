<?php

namespace Tests\Feature\Jobs;

use App\Jobs\CalculateAverageRequestTimeJob;
use App\Models\RequestTiming;
use App\Models\RequestTimingAverage;
use Tests\TestCase;

class CalculateAverageRequestTimeJobTest extends TestCase
{
    public function test_calculates_average_from_recent_request_timings(): void
    {
        // Recent request timings within the last 5 minutes
        RequestTiming::factory()->count(3)->create([
            'duration' => 1.5,
            'created_at' => now()->subMinutes(2),
        ]);
        // Older request timings (should be ignored)
        RequestTiming::factory()->count(2)->create([
            'duration' => 5.0,
            'created_at' => now()->subMinutes(10),
        ]);

        (new CalculateAverageRequestTimeJob)->handle();

        $this->assertDatabaseCount('request_timing_averages', 1);
        $average = RequestTimingAverage::first();
        $this->assertEquals(1.5, $average->average_duration);
    }

    public function test_calculates_correct_average_with_mixed_durations(): void
    {
        // Create request timings with different durations within 5 minutes
        RequestTiming::factory()->create([
            'duration' => 1.0,
            'created_at' => now()->subMinutes(1),
        ]);
        RequestTiming::factory()->create([
            'duration' => 2.0,
            'created_at' => now()->subMinutes(2),
        ]);
        RequestTiming::factory()->create([
            'duration' => 3.0,
            'created_at' => now()->subMinutes(3),
        ]);

        (new CalculateAverageRequestTimeJob)->handle();

        $average = RequestTimingAverage::first();
        $this->assertEquals(2.0, $average->average_duration);
    }
}
