<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_material_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('project_material_id')->constrained('project_materials')->cascadeOnDelete();

            $table->date('tanggal_pengajuan');
            $table->decimal('qty', 14, 2);

            $table->string('catatan')->nullable();

            $table->enum('status', ['pending','approved','rejected'])->default('pending');

            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('approved_at')->nullable();
            $table->string('approval_note')->nullable();

            // link ke stok masuk yang tercipta saat approved (opsional tapi enak)
            $table->foreignId('stock_id')->nullable()->constrained('project_material_stocks')->nullOnDelete();

            $table->timestamps();

            $table->index(['project_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_material_requests');
    }
};
