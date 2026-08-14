<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perfil_permissoes', function (Blueprint $table) {
            $table->id();
            $table->string('perfil')->unique();
            $table->boolean('acervo')->default(true);
            $table->boolean('importar')->default(false);
            $table->boolean('relatorios')->default(true);
            $table->timestamps();
        });

        DB::table('perfil_permissoes')->insert([
            ['perfil' => 'Administrador', 'acervo' => true, 'importar' => true, 'relatorios' => true, 'created_at' => now(), 'updated_at' => now()],
            ['perfil' => 'Curador', 'acervo' => true, 'importar' => true, 'relatorios' => true, 'created_at' => now(), 'updated_at' => now()],
            ['perfil' => 'Consulta', 'acervo' => true, 'importar' => false, 'relatorios' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfil_permissoes');
    }
};
