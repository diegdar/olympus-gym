<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('subscription_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreignId('subscription_id')
                ->constrained()
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->date('start_date')->nullable(false)->now();
            $table->date('end_date');
            $table->dateTime('payment_date')->nullable(false)->now();
            $table->enum('status', ['active', 'inactive', 'suspended'])
                ->default('active')
                ->nullable(false);
            $table->timestamps();

            $table->index('start_date', 'idx_subscription_user_start_date');
            $table->index(['user_id', 'subscription_id'], 'idx_subscription_user_combined');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_user');
    }
}
