<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('project_materials', function (Blueprint $table) {
            $table->decimal('harga', 15, 2)->default(0)->after('qty_estimasi');
        });
    }

    public function down(): void
    {
        Schema::table('project_materials', function (Blueprint $table) {
            $table->dropColumn('harga');
        });
    }
};
