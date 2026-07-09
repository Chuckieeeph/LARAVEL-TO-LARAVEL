<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollment_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('assessment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('enrollment_reference_number')->unique();
            $table->string('student_number');
            $table->string('student_name');
            $table->string('course_code');
            $table->string('course_name');
            $table->string('semester');
            $table->string('school_year');
            $table->unsignedInteger('total_units')->default(0);
            $table->string('enrollment_status');
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
        Schema::dropIfExists('enrollment_logs');
    }
};
