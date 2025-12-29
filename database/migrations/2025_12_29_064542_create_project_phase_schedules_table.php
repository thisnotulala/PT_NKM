<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_phase_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('project_phase_id')->constrained('project_phases')->cascadeOnDelete();

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->unsignedInteger('durasi_hari');

            $table->timestamps();

            // 1 tahap hanya boleh punya 1 jadwal
            $table->unique(['project_id', 'project_phase_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_phase_schedules');
    }
};
