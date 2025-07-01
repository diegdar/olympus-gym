<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivitySchedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'activity_id',
        'start_datetime',        
        'room_id',
        'end_datetime',
        'max_enrollment',
        'current_enrollment'
    ];

    /**
     * Get the activity that owns the ActivitySchedule
     *
     * @return BelongsTo
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Get the room that owns the ActivitySchedule
     *
     * @return BelongsTo
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class,
                'activity_schedule_user'
                )->withTimestamps();
    }
       
}
