<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\SubscriptionUser;

class Subscription extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fee',
        'description',
        'price',
        'duration'
    ];

    /**
     * The users that are subscribed to the Subscription
     *
     * @return BelongsToMany
     */
    public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class)
                ->using(SubscriptionUser::class)
                ->withpivot('start_date', 'end_date', 'payment_date')
                ->withTimestamps();
    }

    /**
     * Translate the 'fee' attribute into Spanish.
     *
     * @return string|null
     */
    public function getFeeTranslatedAttribute(): ?string
    {
        return match ($this->fee) {
            'monthly'   => 'Mensual',
            'quarterly' => 'Trimestral',
            'yearly'    => 'Anual',
            default     => null,
        };
    }

}
