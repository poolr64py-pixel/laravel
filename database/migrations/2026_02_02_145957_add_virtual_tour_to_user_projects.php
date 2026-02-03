<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_projects', function (Blueprint $table) {
            $table->text('virtual_tour_url')->nullable()->after('longitude');
        });
    }

    public function down()
    {
        Schema::table('user_projects', function (Blueprint $table) {
            $table->dropColumn('virtual_tour_url');
        });
    }
};
