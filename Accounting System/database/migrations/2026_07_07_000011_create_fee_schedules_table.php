<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_schedules', function (Blueprint $table): void {
            $table->id();
            $table->string('course_code');
            $table->string('semester');
            $table->string('school_year');
            $table->decimal('per_unit_rate', 12, 2)->default(0);
            $table->decimal('registration_fee', 12, 2)->default(0);
            $table->decimal('miscellaneous_fee', 12, 2)->default(0);
            $table->decimal('laboratory_fee', 12, 2)->default(0);
            $table->decimal('other_fee', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['course_code', 'semester', 'school_year'], 'fee_schedule_scope_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_schedules');
    }
};
