<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaptopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laptops', function (Blueprint $table) {
            $table->bigIncrements('laptop_id');
            $table->string('PIC');
            $table->string('departmen');
            $table->string('type');
            $table->string('serial_number');
            $table->string('inventaris_code');
            $table->string('operating_system');
            $table->unsignedBigInteger('pegawai_id');
            $table->foreign('pegawai_id')->references('pegawai_id')->on('pegawais')
            ->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('laptops');
    }
}
