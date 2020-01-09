<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileLaptopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_laptop', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('laptop_id');
            $table->foreign('laptop_id')->references('laptop_id')->on('laptops')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('file_laptop');
    }
}
