<?php

// database/migrations/2026_01_12_000001_add_progress_log_id_to_project_material_outs.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('project_material_outs', function (Blueprint $table) {
            $table->unsignedBigInteger('progress_log_id')->nullable()->after('project_material_id');

            // sesuaikan nama tabel progress log kamu kalau beda
            $table->foreign('progress_log_id')
                ->references('id')
                ->on('project_phase_progress_logs')
                ->onDelete('cascade');

            $table->unique(['progress_log_id', 'project_material_id'], 'uniq_out_log_material');
        });
    }

    public function down(): void
    {
        Schema::table('project_material_outs', function (Blueprint $table) {
            $table->dropUnique('uniq_out_log_material');
            $table->dropForeign(['progress_log_id']);
            $table->dropColumn('progress_log_id');
        });
    }
};
