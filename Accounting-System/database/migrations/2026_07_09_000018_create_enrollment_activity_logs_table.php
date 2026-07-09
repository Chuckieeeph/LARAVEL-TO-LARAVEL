<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollment_activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('event_id')->unique();
            $table->string('event_type');
            $table->string('routing_key')->nullable();
            $table->string('entity_type')->nullable();
            $table->string('entity_identifier')->nullable();
            $table->string('action')->nullable();
            $table->string('actor_name')->nullable();
            $table->string('processing_status')->default('received');
            $table->text('error_message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_activity_logs');
    }
};
