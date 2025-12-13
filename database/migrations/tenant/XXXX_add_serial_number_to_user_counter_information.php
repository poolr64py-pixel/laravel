<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_counter_information', function (Blueprint $table) {
            $table->integer('serial_number')->default(0)->after('language_id');
        });
    }

    public function down()
    {
        Schema::table('user_counter_information', function (Blueprint $table) {
            $table->dropColumn('serial_number');
        });
    }
};
