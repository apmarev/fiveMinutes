<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('rept', function (Blueprint $table) {
            $table->id();

            $table->integer('manager');
            $table->integer('day');
            $table->integer('month');
            $table->string('month_name');
            $table->integer('year');
            $table->integer('pipeline_id');

            $table->integer('leads_sum_success');
            $table->integer('leads_sum_success_package_1');
            $table->integer('leads_sum_success_package_2');
            $table->integer('leads_sum_success_package_pro');

            $table->integer('leads_count_success_package_1');
            $table->integer('leads_count_success_package_2');
            $table->integer('leads_count_success_package_pro');

            $table->integer('contacts_unique_count_package_1');
            $table->integer('contacts_unique_count_package_2');
            $table->integer('contacts_unique_count_package_pro');

            $table->integer('average_check');

            $table->boolean('lead_poor_quality');

            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('rept');
    }
};
