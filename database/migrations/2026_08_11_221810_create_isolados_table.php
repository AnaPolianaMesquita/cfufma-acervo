<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('isolados', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('genero');
            $table->string('especie');
            $table->string('origem')->nullable();
            $table->string('meio_cultivo')->nullable();
            $table->date('data')->nullable();
            $table->string('conservacao')->nullable();
            $table->string('local')->nullable();
            $table->string('armazenamento')->nullable();
            $table->string('autor')->nullable();
            $table->foreignId('importacao_id')->nullable()->constrained('importacoes')->nullOnDelete();
            $table->timestamps();

            $table->index('genero');
            $table->index('conservacao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('isolados');
    }
};
