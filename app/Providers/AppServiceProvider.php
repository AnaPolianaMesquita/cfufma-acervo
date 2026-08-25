<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Redefinição de senha - MicoNIBA')
                ->greeting('Olá, '.$notifiable->name.'!')
                ->line('Recebemos uma solicitação para redefinir a senha da sua conta na MicoNIBA.')
                ->action('Redefinir senha', $url)
                ->line('Este link expira em '.config('auth.passwords.users.expire').' minutos.')
                ->line('Se você não solicitou a redefinição, pode ignorar este e-mail.');
        });
    }
}
