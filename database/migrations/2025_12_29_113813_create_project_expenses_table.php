<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

            $table->date('tanggal');
            $table->string('kategori'); // Material/SDM/Equipment/Operasional/Lainnya
            $table->decimal('nominal', 15, 2);
            $table->string('keterangan')->nullable();

            // optional link
            $table->foreignId('sdm_id')->nullable()->constrained('sdms')->nullOnDelete();
            $table->foreignId('equipment_id')->nullable()->constrained('equipment')->nullOnDelete();

            // bukti
            $table->string('bukti_path')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['project_id','tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_expenses');
    }
};
