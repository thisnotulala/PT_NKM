<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_sdms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('sdm_id')->constrained('sdms')->cascadeOnDelete();

            // opsional tapi berguna: peran khusus di proyek
            $table->string('peran_di_proyek')->nullable();

            $table->timestamps();

            // ✅ biar tidak dobel
            $table->unique(['project_id','sdm_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_sdms');
    }
};
