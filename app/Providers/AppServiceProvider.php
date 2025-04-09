<?php
declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

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
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->greeting('Hola!')
                ->subject('Verificar correo electrónico')
                ->line('Por favor, haga clic en el botón de abajo para verificar su dirección de correo electrónico.')
                ->action('Verificar correo electrónico', $url)
                ->salutation('Saludos,')
                ->salutation('Laravel');                
        });        
    }
}
