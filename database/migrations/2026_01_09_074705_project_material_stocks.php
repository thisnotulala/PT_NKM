<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_material_stocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            $table->foreignId('project_material_id')
                ->constrained('project_materials')
                ->cascadeOnDelete();

            $table->date('tanggal');
            $table->decimal('qty_masuk', 12, 2)->default(0);
            $table->text('catatan')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'project_material_id'], 'pms_project_material_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_material_stocks');
    }
};
