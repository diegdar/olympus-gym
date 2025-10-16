<?php
declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Carbon\Carbon;
use App\Models\ActivitySchedule;
use App\Observers\ActivityScheduleObserver;
use App\Providers\Google2FAProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TwoFactorAuthenticationProvider::class, function ($app) {
            return new Google2FAProvider();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('es');
        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'es');

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->greeting('Hola!')
                ->subject('Verificar correo electrónico')
                ->line('Por favor, haga clic en el botón de abajo para verificar su dirección de correo electrónico.')
                ->action('Verificar correo electrónico', $url)
                ->salutation('Saludos,')
                ->salutation('Laravel');
    });

    // Observe ActivitySchedule to invalidate cache via version bump
    ActivitySchedule::observe(ActivityScheduleObserver::class);

    }
}
