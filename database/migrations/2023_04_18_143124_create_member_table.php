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
        Schema::create('member', function (Blueprint $table) {
            $table->string('id_member')->primary();
            $table->string('nama');
            $table->date('tgl_lahir');
            $table->string('email');
            $table->string('password');
            $table->string('gender');
            $table->string('no_telp', 15);
            $table->string('alamat');
            $table->boolean('status_membership');
            $table->date('tgl_daftar');
            $table->date('tgl_exp_membership')->nullable();
            $table->float('sisa_deposit', 10, 0)->nullable();
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
        Schema::dropIfExists('member');
    }
};
