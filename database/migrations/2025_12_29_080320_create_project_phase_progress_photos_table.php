<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_phase_progress_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('log_id')->constrained('project_phase_progress_logs')->cascadeOnDelete();
            $table->string('photo_path'); // storage path
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_phase_progress_photos');
    }
};
