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
        Schema::create('deposit_umum', function (Blueprint $table) {
            $table->string('id_deposit_umum')->primary();
            $table->string('id_member')->index('id_member');
            $table->string('id_pegawai')->index('id_pegawai');
            $table->string('id_promo')->index('id_promo');
            $table->date('tgl_transaksi');
            $table->string('no_struk', 15);
            $table->float('deposit_uang', 10, 0);
            $table->float('bonus_deposit', 10, 0);
            $table->float('sisa_deposit', 10, 0);
            $table->float('total_deposit', 10, 0);
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
        Schema::dropIfExists('deposit_umum');
    }
};
