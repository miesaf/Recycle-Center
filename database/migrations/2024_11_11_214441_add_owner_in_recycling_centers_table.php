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
            $table->bigInteger("owner");
            $table->boolean("is_verified")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recycling_centers', function (Blueprint $table) {
            $table->dropColumn("owner");
            $table->dropColumn("is_verified");
        });
    }
};
