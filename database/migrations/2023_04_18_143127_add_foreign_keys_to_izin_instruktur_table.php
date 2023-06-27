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
        Schema::table('izin_instruktur', function (Blueprint $table) {
            $table->foreign(['id_instruktur'], 'izin_instruktur_ibfk_1')->references(['id_instruktur'])->on('instruktur');
            $table->foreign(['id_jadwal_harian'], 'izin_instruktur_ibfk_2')->references(['id_jadwal_harian'])->on('jadwal_harian');
            $table->foreign(['id_instruktur_pengganti'], 'izin_instruktur_ibfk_3')->references(['id_instruktur'])->on('instruktur');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('izin_instruktur', function (Blueprint $table) {
            $table->dropForeign('izin_instruktur_ibfk_1');
            $table->dropForeign('izin_instruktur_ibfk_2');
            $table->dropForeign('izin_instruktur_ibfk_3');
        });
    }
};
