<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Schedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'start_time',
        'day_of_week',
        'end_time',
        'room_id',
    ];

    /**
     * The activity schedules associated with the activity
     *
     * @return BelongsToMany
     */     
    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class);
    } 

    /**
     * The activity schedules associated with the activity
     *
     * @return BelongsToMany
     */     
    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class);
    }   
   
}
