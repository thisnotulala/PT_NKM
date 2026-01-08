<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_material_usages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('progress_log_id')
                ->constrained('project_phase_progress_logs')
                ->cascadeOnDelete();

            $table->foreignId('project_material_id')
                ->constrained('project_materials')
                ->cascadeOnDelete();

            $table->decimal('qty_pakai', 12, 2)->default(0);

            $table->timestamps();

            // ✅ nama index dibuat pendek biar tidak error MySQL
            $table->unique(
                ['progress_log_id', 'project_material_id'],
                'pmu_log_material_unique'
            );

            $table->index(
                ['project_material_id'],
                'pmu_material_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_material_usages');
    }
};
