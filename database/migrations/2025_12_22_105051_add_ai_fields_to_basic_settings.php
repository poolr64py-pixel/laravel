<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('basic_settings', function (Blueprint $table) {
            $table->boolean('ai_generate_status')->default(0)->nullable();
            $table->string('gemini_apikey')->nullable();
            $table->string('gemini_model')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('basic_settings', function (Blueprint $table) {
            $table->dropColumn(['ai_generate_status', 'gemini_apikey', 'gemini_model']);
        });
    }
};
