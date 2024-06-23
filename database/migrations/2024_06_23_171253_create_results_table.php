<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultsTable extends Migration
{
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('patient_identification_number');
            $table->unsignedBigInteger('clinic_id');
            $table->unsignedBigInteger('type_id');
            $table->date('date');
            $table->text('notes')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();

            $table->foreign('clinic_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');
        });

        Schema::create('doctor_result', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('result_id');
            $table->timestamps();

            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('result_id')->references('id')->on('results')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('doctor_result');
        Schema::dropIfExists('results');
    }
}
