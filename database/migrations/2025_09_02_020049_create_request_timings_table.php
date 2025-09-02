<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_timings', function (Blueprint $table) {
            $table->id();
            $table->decimal('duration', 8, 2);
            $table->timestamp('created_at');

            // Index on duration for AVG calculations
            $table->index('duration');

            // Composite index for time-based queries
            $table->index(['duration', 'created_at']);
        });
    }
};
