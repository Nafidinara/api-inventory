<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilePrinterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_printer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('printer_id');
            $table->foreign('printer_id')->references('printer_id')->on('printers')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('file_id');
            $table->foreign('file_id')->references('file_id')->on('files')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_printer');
    }
}
