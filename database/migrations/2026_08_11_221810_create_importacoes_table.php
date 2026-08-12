<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('importacoes', function (Blueprint $table) {
            $table->id();
            $table->string('arquivo');
            $table->unsignedInteger('tamanho')->default(0);
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('concluida');
            $table->unsignedInteger('total_linhas')->default(0);
            $table->unsignedInteger('importados')->default(0);
            $table->unsignedInteger('invalidos')->default(0);
            $table->unsignedInteger('duplicados')->default(0);
            $table->json('colunas_ausentes')->nullable();
            $table->json('linhas_invalidas')->nullable();
            $table->text('erro_mensagem')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('importacoes');
    }
};
