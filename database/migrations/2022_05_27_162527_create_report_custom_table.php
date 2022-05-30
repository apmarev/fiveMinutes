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
        Schema::create('report_custom', function (Blueprint $table) {
            $table->id();
            $table->string('manager');
            $table->string('type')->nullable();
            $table->integer('day')->nullable();
            $table->string('month')->nullable();
            $table->integer('year')->nullable();
            $table->integer('value')->default(0);
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
        Schema::dropIfExists('report_custom');
    }
};
