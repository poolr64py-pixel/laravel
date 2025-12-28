<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_states', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->after('country_id');
        });
    }

    public function down()
    {
        Schema::table('user_states', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
