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
        Schema::create('managers_plan', function (Blueprint $table) {
            $table->id();

            $table->integer('manager_id');
            $table->integer('year');
            $table->integer('month');
            $table->integer('week');
            $table->integer('pipeline_id');

            $table->integer('month_sum')->default(0);
            $table->integer('package_sum')->default(0);
            $table->integer('month_count')->default(0);
            $table->integer('package_count')->default(0); //
            $table->integer('pro_count')->default(0);
            $table->integer('count')->default(0);

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
        Schema::dropIfExists('managers_plan');
    }
};
