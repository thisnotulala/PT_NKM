<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

            $table->string('nama_material');
            $table->string('satuan', 50)->nullable();
            $table->decimal('qty_estimasi', 12, 2)->default(0);

            // opsional: toleransi persen (misal 10)
            $table->unsignedInteger('toleransi_persen')->nullable();

            $table->timestamps();

            $table->index(['project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_materials');
    }
};
