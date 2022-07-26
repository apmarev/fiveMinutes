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
        Schema::create('managers_info', function (Blueprint $table) {
            $table->id();

            $table->integer('manager_id');
            $table->string('manager_name');
            $table->integer('day');
            $table->integer('month');
            $table->string('month_name');
            $table->integer('year');

            $table->integer('leads_count');
            $table->integer('sum_month');
            $table->integer('sum_package');
            $table->integer('sum_pro');
            $table->integer('count');
            $table->integer('count_month');
            $table->integer('count_package');
            $table->integer('count_pro');
            $table->integer('count_clients_month');
            $table->integer('count_clients_package');
            $table->integer('count_clients_pro');
            $table->integer('children_ege');
            $table->integer('children_oge');
            $table->integer('children_10');
            $table->integer('parents_ege');
            $table->integer('parents_oge');
            $table->integer('parents_10');
            $table->integer('children_month_ege');
            $table->integer('children_month_oge');
            $table->integer('children_month_10');
            $table->integer('parents_month_ege');
            $table->integer('parents_month_oge');
            $table->integer('parents_month_10');
            $table->integer('children_package_ege');
            $table->integer('children_package_oge');
            $table->integer('children_package_10');
            $table->integer('parents_package_ege');
            $table->integer('parents_package_oge');
            $table->integer('parents_package_10');
            $table->integer('count_children_none');
            $table->integer('count_children_ege');
            $table->integer('count_children_oge');
            $table->integer('count_children_10');
            $table->integer('count_parents_none');
            $table->integer('count_parents_ege');
            $table->integer('count_parents_oge');
            $table->integer('count_parents_10');
            $table->integer('count_sale_children_ege');
            $table->integer('count_sale_children_oge');
            $table->integer('count_sale_children_10');
            $table->integer('count_sale_parents_ege');
            $table->integer('count_sale_parents_oge');
            $table->integer('count_sale_parents_10');
            $table->integer('unique_children_ege');
            $table->integer('unique_children_oge');
            $table->integer('unique_children_10');
            $table->integer('unique_parents_ege');
            $table->integer('unique_parents_oge');
            $table->integer('unique_parents_10');
            $table->integer('average_check');
            $table->integer('average_check_children_ege');
            $table->integer('average_check_children_oge');
            $table->integer('average_check_children_10');
            $table->integer('average_check_parents_ege');
            $table->integer('average_check_parents_oge');
            $table->integer('average_check_parents_10');
            $table->integer('substandard_leads');

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
        Schema::dropIfExists('managers_info');
    }
};
