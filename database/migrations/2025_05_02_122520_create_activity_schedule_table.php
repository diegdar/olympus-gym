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
                        ->index('idx_activity_schedule_activity_id');
            $table->foreignId('room_id')
                    ->constrained('rooms')
                        ->cascadeOnDelete()
                        ->cascadeOnUpdate()
                        ->index('idx_activity_schedule_room_id');
            $table->enum('day_of_week', [
                'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'
            ])->nullable(false)
              ->index('idx_schedules_day_of_week');
            $table->time('start_time')
            ->nullable(false)
            ->index('idx_schedules_start_time');
            $table->time('end_time')
                ->nullable(false);
            $table->integer('max_enrollment')
                ->nullable(false)
                ->unsigned()
                ->default(0);
            $table->integer('current_enrollment')
                ->nullable(false)
                ->unsigned()
                ->default(0);
            $table->timestamps();

            $table->unique(['start_time', 'day_of_week', 'room_id'], 'uk_activity_schedules_start_time_day_of_week_room_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_schedule');
    }
};
