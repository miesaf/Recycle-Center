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
        Schema::table('recycling_centers', function (Blueprint $table) {
            $table->string("latitude");
            $table->string("longitude");
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recycling_centers', function (Blueprint $table) {
            $table->dropColumn("latitude");
            $table->dropColumn("longitude");
            $table->dropSoftDeletes();
        });
    }
};
