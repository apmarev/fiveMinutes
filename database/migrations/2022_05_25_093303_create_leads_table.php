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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->integer('leadId');
            $table->integer('price');
            $table->integer('userId');
            $table->integer('statusId');
            $table->integer('pipelineId');
            $table->integer('createdAt');
            $table->timestamps();
        });

        Schema::create('leads_custom', function (Blueprint $table) {
            $table->id();
            $table->integer('leadId');
            $table->integer('fieldId');
            $table->string('value')->nullable();
            $table->integer('enum')->nullable();
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
        Schema::dropIfExists('leads');
        Schema::dropIfExists('leads_custom');
    }
};
