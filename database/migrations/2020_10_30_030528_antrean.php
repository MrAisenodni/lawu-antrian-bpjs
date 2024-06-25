<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Antrean extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('antreans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_booking');
            $table->string('kode_poli');
            $table->string('nik');
            $table->date('tanggal_periksa');
            $table->string('no_antrean');
            $table->string('no_kartu');
            $table->string('no_telp');
            $table->string('no_referensi');
            $table->integer('jenis_referensi');
            $table->integer('jenis_request');
            $table->integer('poli_eksekutif');
            $table->date('estimasi_dilayani');
            $table->integer('sudah_dilayani')->default(0);
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
        Schema::dropIfExists('antreans');
    }
}
