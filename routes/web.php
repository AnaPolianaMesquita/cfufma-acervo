<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\AcervoController;
use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GaleriaController;
use App\Http\Controllers\ImportacaoController;
use App\Http\Controllers\RelatorioController;

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : redirect()->route('galeria.index'));

// Galeria pública (sem login)
Route::prefix('galeria')->name('galeria.')->group(function () {
    Route::get('/', [GaleriaController::class, 'index'])->name('index');
    Route::get('/{id}', [GaleriaController::class, 'show'])->whereNumber('id')->name('show');
});

// Autenticação
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.attempt');

    Route::get('/registrar', [RegisterController::class, 'index'])->name('register');
    Route::post('/registrar', [RegisterController::class, 'store'])->name('register.store');

    Route::get('/esqueci-senha', [ForgotPasswordController::class, 'index'])->name('password.request');
    Route::post('/esqueci-senha', [ForgotPasswordController::class, 'send'])->name('password.email');
    Route::get('/redefinir-senha/{token}', [ResetPasswordController::class, 'index'])->name('password.reset');
    Route::post('/redefinir-senha', [ResetPasswordController::class, 'update'])->name('password.update');
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Importação (permissão por perfil, ver Configurações → Usuários)
    Route::prefix('importacao')->name('importacao.')->middleware('modulo:importar')->group(function () {
        Route::get('/', [ImportacaoController::class, 'index'])->name('index');
        Route::post('/preview', [ImportacaoController::class, 'preview'])->name('preview');
        Route::post('/', [ImportacaoController::class, 'store'])->name('store');
        Route::get('/historico', [ImportacaoController::class, 'historico'])->name('historico');
        Route::get('/{id}', [ImportacaoController::class, 'show'])->whereNumber('id')->name('show');
    });

    // Acervo (permissão por perfil, ver Configurações → Usuários)
    Route::prefix('acervo')->name('acervo.')->middleware('modulo:acervo')->group(function () {
        Route::get('/', [AcervoController::class, 'index'])->name('index');
        Route::get('/criar', [AcervoController::class, 'create'])->name('create');
        Route::post('/', [AcervoController::class, 'store'])->name('store');
        Route::delete('/em-massa', [AcervoController::class, 'destroyMultiple'])->name('destroyEmMassa');
        Route::get('/{id}/editar', [AcervoController::class, 'edit'])->whereNumber('id')->name('edit');
        Route::put('/{id}', [AcervoController::class, 'update'])->whereNumber('id')->name('update');
        Route::delete('/{id}', [AcervoController::class, 'destroy'])->whereNumber('id')->name('destroy');
        Route::get('/{id}', [AcervoController::class, 'show'])->whereNumber('id')->name('show');
    });

    // Relatórios (permissão por perfil, ver Configurações → Usuários)
    Route::prefix('relatorios')->name('relatorios.')->middleware('modulo:relatorios')->group(function () {
        Route::get('/', [RelatorioController::class, 'index'])->name('index');
        Route::get('/exportar/excel', [RelatorioController::class, 'exportarExcel'])->name('exportar.excel');
        Route::get('/exportar/pdf', [RelatorioController::class, 'exportarPdf'])->name('exportar.pdf');
    });

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
            Route::put('/permissoes', [ConfiguracaoController::class, 'permissoesUpdate'])->name('permissoes.update');
        });
    });
});
