<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AcervoController;
use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportacaoController;
use App\Http\Controllers\RelatorioController;

Route::redirect('/', '/dashboard');

// Autenticação
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.attempt');

    Route::get('/registrar', [RegisterController::class, 'index'])->name('register');
    Route::post('/registrar', [RegisterController::class, 'store'])->name('register.store');
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Importação (Administrador e Curador)
    Route::prefix('importacao')->name('importacao.')->middleware('perfil:Administrador,Curador')->group(function () {
        Route::get('/', [ImportacaoController::class, 'index'])->name('index');
        Route::post('/preview', [ImportacaoController::class, 'preview'])->name('preview');
        Route::post('/', [ImportacaoController::class, 'store'])->name('store');
        Route::get('/historico', [ImportacaoController::class, 'historico'])->name('historico');
        Route::get('/{id}', [ImportacaoController::class, 'show'])->whereNumber('id')->name('show');
    });

    // Acervo
    Route::prefix('acervo')->name('acervo.')->group(function () {
        Route::get('/', [AcervoController::class, 'index'])->name('index');
        Route::get('/criar', [AcervoController::class, 'create'])->name('create');
        Route::post('/', [AcervoController::class, 'store'])->name('store');
        Route::delete('/em-massa', [AcervoController::class, 'destroyMultiple'])->name('destroyEmMassa');
        Route::get('/{id}/editar', [AcervoController::class, 'edit'])->whereNumber('id')->name('edit');
        Route::put('/{id}', [AcervoController::class, 'update'])->whereNumber('id')->name('update');
        Route::delete('/{id}', [AcervoController::class, 'destroy'])->whereNumber('id')->name('destroy');
        Route::get('/{id}', [AcervoController::class, 'show'])->whereNumber('id')->name('show');
    });

    // Relatórios
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');

    // Configurações
    Route::prefix('configuracoes')->name('configuracoes.')->group(function () {
        Route::get('/', [ConfiguracaoController::class, 'index'])->name('index');
        Route::put('/', [ConfiguracaoController::class, 'updatePerfil'])->name('update');
        Route::put('/senha', [ConfiguracaoController::class, 'updateSenha'])->name('senha.update');

        Route::middleware('perfil:Administrador')->group(function () {
            Route::get('/usuarios', [ConfiguracaoController::class, 'usuarios'])->name('usuarios');
            Route::post('/usuarios', [ConfiguracaoController::class, 'usuariosStore'])->name('usuarios.store');
            Route::put('/usuarios/{id}', [ConfiguracaoController::class, 'usuariosUpdate'])->whereNumber('id')->name('usuarios.update');
            Route::patch('/usuarios/{id}/status', [ConfiguracaoController::class, 'usuariosToggle'])->whereNumber('id')->name('usuarios.toggle');
            Route::delete('/usuarios/{id}', [ConfiguracaoController::class, 'usuariosDestroy'])->whereNumber('id')->name('usuarios.destroy');
        });
    });
});
