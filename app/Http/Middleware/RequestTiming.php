<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestTiming
{
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);
        $response = $next($request);
        $duration = microtime(true) - $start;

        try {
            \App\Models\RequestTiming::query()->create([
                'duration' => $duration,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to log request timing: '.$e->getMessage());
        }

        return $response;
    }
}
