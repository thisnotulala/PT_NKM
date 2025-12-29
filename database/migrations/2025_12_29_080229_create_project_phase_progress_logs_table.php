<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_phase_progress_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('project_phase_id')->constrained('project_phases')->cascadeOnDelete();

            $table->date('tanggal_update');
            $table->unsignedInteger('progress'); // progress saat itu (0..100)
            $table->text('catatan')->nullable();

            $table->unsignedBigInteger('created_by')->nullable(); // optional
            $table->timestamps();

            $table->index(['project_id','project_phase_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_phase_progress_logs');
    }
};
