<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Subscription;
use App\Models\SubscriptionUser;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    'birth_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function subscriptions(): BelongsToMany
    {
        return $this->belongsToMany(Subscription::class)
                ->using(SubscriptionUser::class)
                ->withPivot('start_date', 'end_date', 'payment_date', 'status')
                ->withTimestamps();
    }

    public function activySchedules(): BelongsToMany
    {
        return $this->belongsToMany
                (ActivitySchedule::class,    'activity_schedule_user'
                )->withTimestamps();
    }

    public function subscribeTo(Subscription $subscription, ?string $paymentDate = null)
    {
        if ($this->subscriptions->contains($subscription)) {
            return false;
        }

        $startDate = Carbon::now();

        $endDate = $startDate->copy()->addMonths($subscription->duration);

        $this->subscriptions()->attach($subscription, [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'payment_date' => $paymentDate ?? Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        return true;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date' => 'date',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }
}
