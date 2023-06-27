<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aktivasi_tahunan', function (Blueprint $table) {
            $table->string('id_aktivasi')->primary();
            $table->string('id_member')->index('id_member');
            $table->string('id_pegawai')->index('aktivasi tahunan_ibfk_1');
            $table->date('tgl_transaksi');
            $table->date('masa_aktif')->nullable();
            $table->string('no_struk', 15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aktivasi_tahunan');
    }
};
