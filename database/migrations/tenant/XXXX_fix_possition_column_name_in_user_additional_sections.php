<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_additional_sections', function (Blueprint $table) {
            $table->renameColumn('possition', 'position');
        });
    }

    public function down()
    {
        Schema::table('user_additional_sections', function (Blueprint $table) {
            $table->renameColumn('position', 'possition');
        });
    }
};
