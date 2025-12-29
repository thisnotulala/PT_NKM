<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('project_phases', function (Blueprint $table) {
            $table->unsignedInteger('progress')->default(0)->after('persen'); // 0..100
            $table->date('last_progress_at')->nullable()->after('progress');
        });
    }

    public function down(): void
    {
        Schema::table('project_phases', function (Blueprint $table) {
            $table->dropColumn(['progress','last_progress_at']);
        });
    }
};
