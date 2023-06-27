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
        Schema::table('aktivasi_tahunan', function (Blueprint $table) {
            $table->foreign(['id_pegawai'], 'aktivasi_tahunan_ibfk_1')->references(['id_pegawai'])->on('pegawai');
            $table->foreign(['id_member'], 'aktivasi_tahunan_ibfk_2')->references(['id_member'])->on('member');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aktivasi_tahunan', function (Blueprint $table) {
            $table->dropForeign('aktivasi_tahunan_ibfk_1');
            $table->dropForeign('aktivasi_tahunan_ibfk_2');
        });
    }
};
