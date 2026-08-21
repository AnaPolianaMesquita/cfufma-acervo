<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function index(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'unique:users,email'],
            'senha' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $perfil = str_ends_with(strtolower($validated['email']), '@discente.ufma.br')
            ? 'Curador'
            : 'Consulta';

        $user = User::create([
            'name' => $validated['nome'],
            'email' => $validated['email'],
            'password' => $validated['senha'],
            'perfil' => $perfil,
            'ativo' => true,
            'ultimo_acesso' => now(),
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Conta criada com sucesso. Bem-vindo(a)!');
    }
}
