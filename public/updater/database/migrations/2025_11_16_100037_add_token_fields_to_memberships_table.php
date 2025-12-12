<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->integer('total_tokens')->default(0)->after('expire_date')
                ->comment('Total tokens allocated with this membership');
            $table->integer('used_tokens')->default(0)->after('total_tokens')
                ->comment('Tokens already consumed by user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn(['total_tokens', 'used_tokens']);
        });
    }
};
