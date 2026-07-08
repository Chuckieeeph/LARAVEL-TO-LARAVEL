<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('enrollment_reference_number')->unique();
            $table->string('course_code');
            $table->string('course_name');
            $table->string('semester');
            $table->string('school_year');
            $table->unsignedInteger('total_units')->default(0);
            $table->decimal('per_unit_rate', 12, 2)->default(0);
            $table->decimal('tuition_fee', 12, 2)->default(0);
            $table->decimal('registration_fee', 12, 2)->default(0);
            $table->decimal('miscellaneous_fee', 12, 2)->default(0);
            $table->decimal('laboratory_fee', 12, 2)->default(0);
            $table->decimal('other_fee', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('status')->default('pending');
            $table->json('payload')->nullable();
            $table->timestamp('assessed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
