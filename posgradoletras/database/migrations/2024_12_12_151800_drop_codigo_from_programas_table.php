<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Drop the codigo column from programas table since we now use nombre for slug generation.
     */
    public function up(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->dropColumn('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->string('codigo', 50)->nullable()->after('id');
        });
    }
};
