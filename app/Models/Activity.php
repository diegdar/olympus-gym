<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
     * @return BelongsToMany
     */     
    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(ActivitySchedule::class);
    }
}
