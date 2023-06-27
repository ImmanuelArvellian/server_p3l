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
        Schema::create('booking_kelas', function (Blueprint $table) {
            $table->string('id_booking_kelas')->primary();
            $table->string('id_member')->index('id_member');
            $table->string('id_jadwal_harian')->index('id_jadwal_harian');
            $table->string('no_struk');
            $table->date('tgl_tujuan');
            $table->string('tipe_pembayaran');
            $table->dateTime('tgl_presensi')->nullable();
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
        Schema::dropIfExists('booking_kelas');
    }
};
