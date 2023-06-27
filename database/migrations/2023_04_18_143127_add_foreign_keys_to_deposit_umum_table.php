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
        Schema::table('deposit_umum', function (Blueprint $table) {
            $table->foreign(['id_pegawai'], 'deposit_umum_ibfk_1')->references(['id_pegawai'])->on('pegawai');
            $table->foreign(['id_promo'], 'deposit_umum_ibfk_3')->references(['id_promo'])->on('promo');
            $table->foreign(['id_member'], 'deposit_umum_ibfk_2')->references(['id_member'])->on('member');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deposit_umum', function (Blueprint $table) {
            $table->dropForeign('deposit_umum_ibfk_1');
            $table->dropForeign('deposit_umum_ibfk_3');
            $table->dropForeign('deposit_umum_ibfk_2');
        });
    }
};
