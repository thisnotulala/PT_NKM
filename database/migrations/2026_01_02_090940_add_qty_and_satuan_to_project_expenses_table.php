<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQtyAndSatuanToProjectExpensesTable extends Migration
{
    public function up()
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->decimal('qty', 12, 2)->nullable()->after('kategori');
            $table->unsignedBigInteger('satuan_id')->nullable()->after('qty');

            // sesuaikan nama tabel satuan kamu (biasanya: satuans)
            $table->foreign('satuan_id')->references('id')->on('satuans')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->dropForeign(['satuan_id']);
            $table->dropColumn(['qty','satuan_id']);
        });
    }
}
