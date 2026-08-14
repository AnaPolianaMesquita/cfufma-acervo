<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function index(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'senha' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            [
                'email' => $validated['email'],
                'password' => $validated['senha'],
                'password_confirmation' => $request->input('senha_confirmation'),
                'token' => $validated['token'],
            ],
            function (User $user, string $password) {
                $user->update(['password' => $password]);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withInput($request->only('email'))->with('error', 'Este link de redefinição é inválido ou expirou. Solicite um novo.');
        }

        return redirect()->route('login')->with('success', 'Senha redefinida com sucesso. Faça login com a nova senha.');
    }
}
