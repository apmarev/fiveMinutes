<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up() {
        Schema::create('plan', function (Blueprint $table) {
            $table->id();

            $table->integer('manager');

            $table->integer('month_week_1');
            $table->integer('month_week_2');
            $table->integer('month_week_3');
            $table->integer('month_week_4');

            $table->integer('sum_week_1');
            $table->integer('sum_week_2');
            $table->integer('sum_week_3');
            $table->integer('sum_week_4');

            $table->integer('count_week_1');
            $table->integer('count_week_2');
            $table->integer('count_week_3');
            $table->integer('count_week_4');

            $table->integer('sale_week_1');
            $table->integer('sale_week_2');
            $table->integer('sale_week_3');
            $table->integer('sale_week_4');

            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('plan');
    }
};
