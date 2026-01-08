<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_phase_progress_log_sdms', function (Blueprint $table) {
            $table->id();

            $table->foreignId('log_id')
                ->constrained('project_phase_progress_logs')
                ->cascadeOnDelete();

            $table->foreignId('sdm_id')
                ->constrained('sdms')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['log_id', 'sdm_id']);
            $table->index(['sdm_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_phase_progress_log_sdms');
    }
};
