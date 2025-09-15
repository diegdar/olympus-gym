<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')
                    ->constrained('activities')
                        ->cascadeOnDelete()
                        ->cascadeOnUpdate()
                        ->index('idx_activity_schedules_activity_id');
            $table->dateTime('start_datetime')
                ->nullable(false)
                ->index('idx_activity_schedules_start_time');
            $table->foreignId('room_id')
                ->constrained('rooms')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate()
                    ->index('idx_activity_schedules_room_id');
            $table->dateTime('end_datetime')
                ->nullable(false);
            $table->unsignedTinyInteger('max_enrollment')
                ->comment('Capacidad máxima de plazas para la sesión');
            $table->timestamps();

            $table->unique(['start_datetime', 'room_id'], 'idx_activity_schedule_start_time_room_id');
            $table->index(['activity_id','start_datetime'], 'idx_activity_schedules_activity_start');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_schedules');
    }
};
