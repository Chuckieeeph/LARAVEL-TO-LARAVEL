<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            $table->string('gender')->nullable()->after('last_name');
            $table->date('birth_date')->nullable()->after('gender');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            $table->dropColumn(['gender', 'birth_date', 'phone', 'address']);
        });
    }
};
