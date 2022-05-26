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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            $table->string('manager');

            $table->string('monthName');
            $table->string('month');
            $table->integer('year');
            $table->integer('pipelineId');

            $table->float('all')->default(0);
            $table->float('monthExam')->default(0);
            $table->float('monthOge')->default(0);
            $table->float('monthTenClass')->default(0);
            $table->float('packageExam')->default(0);
            $table->float('packageOge')->default(0);
            $table->float('packageTenClass')->default(0);
            $table->float('countPackagesExam')->default(0);
            $table->float('countPackagesOge')->default(0);
            $table->float('countPackagesTenClass')->default(0);
            $table->float('countPriceMonth')->default(0);
            $table->float('countPricePackage')->default(0);
            $table->float('countMonth')->default(0);
            $table->float('countPackage')->default(0);
            $table->float('averageCheck')->default(0);
            $table->float('conversion')->default(0);

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
        Schema::dropIfExists('reports');
    }
};
