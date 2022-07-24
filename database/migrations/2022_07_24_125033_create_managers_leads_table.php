<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up() {
        Schema::create('managers_leads', function (Blueprint $table) {
            $table->id();

            $table->integer('manager');
            $table->integer('pipeline_id');

            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('managers_leads');
    }
};
