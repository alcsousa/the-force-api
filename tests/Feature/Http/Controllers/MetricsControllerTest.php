<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\RequestTimingAverage;
use Tests\TestCase;

class MetricsControllerTest extends TestCase
{
    public function test_metrics_index_returns_average_duration()
    {
        $averageDuration = 12.34;
        RequestTimingAverage::create([
            'average_duration' => $averageDuration,
        ]);

        $response = $this->getJson('/api/v1/average-request-timing');

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'average_duration' => $averageDuration,
                ],
            ]);
    }
}
