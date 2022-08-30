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
        Schema::table('managers_info', function (Blueprint $table) {
            $table->integer('count_none_ege');
            $table->integer('count_none_oge');
            $table->integer('count_none_10');
            $table->integer('count_none_none');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('managers_info', function (Blueprint $table) {
            $table->dropColumn('count_none_ege');
            $table->dropColumn('count_none_oge');
            $table->dropColumn('count_none_10');
            $table->dropColumn('count_none_none');
        });
    }
};
