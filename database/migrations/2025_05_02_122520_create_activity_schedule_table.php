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
        Schema::create('activity_schedule', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')
                    ->constrained('activities')
                        ->cascadeOnDelete()
                        ->cascadeOnUpdate()
                        ->index('idx_activity_schedule_activity_id');
            $table->foreignId('schedule_id')
                    ->constrained('schedules')
                        ->cascadeOnDelete()
                        ->cascadeOnUpdate()
                        ->index('idx_activity_schedule_schedule_id');
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

            $table->unique(['activity_id', 'schedule_id'], 'uk_activity_schedule_activity_id_schedule_id');
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
