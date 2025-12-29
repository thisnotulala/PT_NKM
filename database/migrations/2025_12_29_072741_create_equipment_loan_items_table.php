<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('equipment_loan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('equipment_loans')->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();

            $table->unsignedInteger('qty'); // qty dipinjam

            // qty kembali dibagi agar bisa rusak/hilang sebagian
            $table->unsignedInteger('qty_baik')->default(0);
            $table->unsignedInteger('qty_rusak')->default(0);
            $table->unsignedInteger('qty_hilang')->default(0);

            $table->text('catatan_kondisi')->nullable();
            $table->timestamps();

            $table->unique(['loan_id','equipment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_loan_items');
    }
};
