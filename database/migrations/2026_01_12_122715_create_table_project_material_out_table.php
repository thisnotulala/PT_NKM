<?php

// database/migrations/2026_01_12_000000_create_project_material_outs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_material_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_material_id')->constrained('project_materials')->cascadeOnDelete();

            $table->date('tanggal');
            $table->decimal('qty_keluar', 18, 2);
            $table->string('catatan', 255)->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(
            ['project_id', 'project_material_id', 'tanggal'],
            'idx_pm_out_proj_mat_date'
        );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_material_outs');
    }
};
