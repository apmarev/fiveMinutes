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
        Schema::create('managers_leads_success', function (Blueprint $table) {
            $table->id();

            $table->integer('lead_id');
            $table->integer('price')->nullable();
            $table->integer('manager');
            $table->integer('contact')->nullable();
            $table->integer('pipeline_id');
            $table->integer('status_id');
            $table->boolean('target');
            $table->string('type');
            $table->integer('created');

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
        Schema::dropIfExists('managers_leads_success');
    }
};
