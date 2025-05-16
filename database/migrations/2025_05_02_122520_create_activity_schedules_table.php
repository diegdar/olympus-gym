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
            $table->integer('max_enrollment')
                ->nullable(false)
                ->unsigned()
                ->default(0);
            $table->integer('current_enrollment')
                ->nullable(false)
                ->unsigned()
                ->default(0);
            $table->timestamps();

            $table->unique(['start_datetime', 'room_id'], 'idx_activity_schedule_start_time_room_id');
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
