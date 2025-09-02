<?php

use App\Jobs\CalculateAverageRequestTimeJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new CalculateAverageRequestTimeJob)->everyFiveMinutes();
