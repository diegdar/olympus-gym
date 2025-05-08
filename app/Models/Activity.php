<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'duration',
    ];    

    /**
     * The activity schedules associated with the activity
     *
     * @return HasMany
     */     
    public function activitySchedules(): HasMany
    {
        return $this->hasMany(HasMany::class);
    }
}
