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
        Schema::create('deposit_kelas', function (Blueprint $table) {
            $table->string('id_deposit_kelas')->primary();
            $table->string('id_member')->index('id_member');
            $table->string('id_pegawai')->index('id_pegawai');
            $table->string('id_kelas')->index('id_kelas');
            $table->string('id_promo_kelas')->index('id_promo_kelas');
            $table->date('tgl_transaksi');
            $table->string('no_struk', 15);
            $table->float('uang_deposit_kelas', 10, 0);
            $table->integer('bonus_deposit_kelas');
            $table->integer('sisa_deposit_kelas');
            $table->integer('total_deposit_kelas');
            $table->date('masa_exp');
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
        Schema::dropIfExists('deposit_kelas');
    }
};
