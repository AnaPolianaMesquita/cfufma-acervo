<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConfiguracaoController extends Controller
{
    public function index(): View
    {
        return view('configuracoes.index', [
            'usuario' => Auth::user(),
        ]);
    }

    public function updatePerfil(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->update([
            'name' => $validated['nome'],
            'email' => $validated['email'],
        ]);

        return back()->with('success', 'Perfil atualizado com sucesso.');
    }

    public function updateSenha(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'senha_atual' => ['required'],
            'nova_senha' => ['required', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['senha_atual'], $user->password)) {
            throw ValidationException::withMessages([
                'senha_atual' => 'Senha atual incorreta.',
            ]);
        }

        $user->update(['password' => $validated['nova_senha']]);

        return back()->with('success', 'Senha alterada com sucesso.');
    }

    public function usuarios(): View
    {
        return view('configuracoes.usuarios', [
            'usuarios' => User::orderBy('name')->get(),
        ]);
    }

    public function usuariosStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'unique:users,email'],
            'perfil' => ['required', 'string', Rule::in(['Administrador', 'Curador', 'Consulta'])],
            'senha' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'name' => $validated['nome'],
            'email' => $validated['email'],
            'perfil' => $validated['perfil'],
            'password' => $validated['senha'],
            'ativo' => true,
        ]);

        return redirect()->route('configuracoes.usuarios')->with('success', 'Usuário criado com sucesso.');
    }

    public function usuariosUpdate(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'perfil' => ['required', 'string', Rule::in(['Administrador', 'Curador', 'Consulta'])],
        ]);

        $user->update([
            'name' => $validated['nome'],
            'email' => $validated['email'],
            'perfil' => $validated['perfil'],
        ]);

        return redirect()->route('configuracoes.usuarios')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function usuariosToggle(int $id): RedirectResponse
    {
        if ($id === Auth::id()) {
            return redirect()->route('configuracoes.usuarios')->with('error', 'Você não pode desativar a própria conta.');
        }

        $user = User::findOrFail($id);
        $user->update(['ativo' => ! $user->ativo]);

        return redirect()->route('configuracoes.usuarios')->with('success', 'Status do usuário atualizado.');
    }

    public function usuariosDestroy(int $id): RedirectResponse
    {
        if ($id === Auth::id()) {
            return redirect()->route('configuracoes.usuarios')->with('error', 'Você não pode remover a própria conta.');
        }

        User::findOrFail($id)->delete();

        return redirect()->route('configuracoes.usuarios')->with('success', 'Usuário removido com sucesso.');
    }
}
