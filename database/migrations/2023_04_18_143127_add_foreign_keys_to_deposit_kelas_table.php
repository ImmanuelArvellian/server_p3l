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
        Schema::table('deposit_kelas', function (Blueprint $table) {
            $table->foreign(['id_pegawai'], 'deposit_kelas_ibfk_1')->references(['id_pegawai'])->on('pegawai');
            $table->foreign(['id_member'], 'deposit_kelas_ibfk_2')->references(['id_member'])->on('member');
            $table->foreign(['id_kelas'], 'deposit_kelas_ibfk_3')->references(['id_kelas'])->on('kelas');
            $table->foreign(['id_promo_kelas'], 'deposit_kelas_ibfk_4')->references(['id_promo_kelas'])->on('promo_kelas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deposit_kelas', function (Blueprint $table) {
            $table->dropForeign('deposit_kelas_ibfk_1');
            $table->dropForeign('deposit_kelas_ibfk_3');
            $table->dropForeign('deposit_kelas_ibfk_2');
            $table->dropForeign('deposit_kelas_ibfk_4');
        });
    }
};
