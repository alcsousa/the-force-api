<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_timing_averages', function (Blueprint $table) {
            $table->id();
            $table->decimal('average_duration', 8, 2);
            $table->timestamp('created_at');

            $table->index('created_at');
        });
    }
};
