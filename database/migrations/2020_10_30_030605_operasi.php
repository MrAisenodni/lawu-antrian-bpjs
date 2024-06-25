<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Operasi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operasis', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_booking');
            $table->string('kode_poli');
            $table->date('tanggal_operasi');
            $table->string('no_peserta')->nullable();
            $table->string('nama_dokter')->nullable();
            $table->string('jenis_tindakan')->nullable();
            $table->integer('sudah_dilaksanakan')->default(0);
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
        Schema::dropIfExists('operasis');
    }
}
