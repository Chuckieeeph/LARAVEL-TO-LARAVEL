<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table): void {
            $table->id();
            $table->string('enrollment_reference_number')->unique();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('student_number');
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('semester');
            $table->string('school_year');
            $table->string('status')->default('enrolled');
            $table->unsignedInteger('total_units')->default(0);
            $table->string('created_by_name')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
