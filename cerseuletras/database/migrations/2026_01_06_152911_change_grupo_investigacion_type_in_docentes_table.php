<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add temporary column
        Schema::table('docentes', function (Blueprint $table) {
            $table->json('grupo_investigacion_json')->nullable()->after('grupo_investigacion');
        });

        // 2. Migrate existing data
        DB::table('docentes')->whereNotNull('grupo_investigacion')->get()->each(function ($docente) {
            DB::table('docentes')->where('id', $docente->id)->update([
                'grupo_investigacion_json' => json_encode([
                    'nombre' => $docente->grupo_investigacion,
                    'enlace' => ''
                ])
            ]);
        });

        // 3. Drop old column and rename new one
        Schema::table('docentes', function (Blueprint $table) {
            $table->dropColumn('grupo_investigacion');
        });

        Schema::table('docentes', function (Blueprint $table) {
            $table->renameColumn('grupo_investigacion_json', 'grupo_investigacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('docentes', function (Blueprint $table) {
            $table->string('grupo_investigacion_str', 255)->nullable()->after('grupo_investigacion');
        });

        DB::table('docentes')->whereNotNull('grupo_investigacion')->get()->each(function ($docente) {
            $json = json_decode($docente->grupo_investigacion, true);
            DB::table('docentes')->where('id', $docente->id)->update([
                'grupo_investigacion_str' => $json['nombre'] ?? ''
            ]);
        });

        Schema::table('docentes', function (Blueprint $table) {
            $table->dropColumn('grupo_investigacion');
        });

        Schema::table('docentes', function (Blueprint $table) {
            $table->renameColumn('grupo_investigacion_str', 'grupo_investigacion');
        });
    }
};
