<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RecuperacaoSenhaTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_recebe_notificacao_ao_solicitar_recuperacao(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'ana@example.com']);

        $response = $this->post(route('password.email'), ['email' => $user->email]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_usuario_pode_redefinir_a_senha_com_token_valido(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'ana@example.com', 'password' => bcrypt('senha-antiga')]);

        $this->post(route('password.email'), ['email' => $user->email]);

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use (&$token) {
            $token = $notification->token;

            return true;
        });

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'senha' => 'nova-senha-123',
            'senha_confirmation' => 'nova-senha-123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('nova-senha-123', $user->fresh()->password));
    }

    public function test_token_invalido_nao_redefine_a_senha(): void
    {
        $user = User::factory()->create(['email' => 'ana@example.com', 'password' => bcrypt('senha-antiga')]);

        $response = $this->post(route('password.update'), [
            'token' => 'token-invalido',
            'email' => $user->email,
            'senha' => 'nova-senha-123',
            'senha_confirmation' => 'nova-senha-123',
        ]);

        $response->assertSessionHas('error');
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('senha-antiga', $user->fresh()->password));
    }
}
