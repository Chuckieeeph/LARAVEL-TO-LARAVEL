<?php

namespace Database\Seeders;

use App\Models\FeeSchedule;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ([
            ['name' => 'Accounting Admin', 'email' => 'admin@accounting.test', 'role' => 'Accounting Administrator'],
            ['name' => 'Cashier User', 'email' => 'cashier@accounting.test', 'role' => 'Cashier'],
            ['name' => 'Accounting Staff', 'email' => 'staff@accounting.test', 'role' => 'Accounting Staff'],
        ] as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => 'password',
                    'role' => $user['role'],
                ]
            );
        }

        foreach ([
            [
                'student_number' => '2026-0001',
                'first_name' => 'Mark',
                'middle_name' => null,
                'last_name' => 'Fillartos',
                'course_code' => 'BSIT',
                'course_name' => 'Bachelor of Science in Information Technology',
                'year_level' => 4,
                'email' => 'mark.fillartos@example.com',
                'status' => 'active',
            ],
            [
                'student_number' => '2026-0002',
                'first_name' => 'Eleah Camille V.',
                'middle_name' => null,
                'last_name' => 'Carillo',
                'course_code' => 'BSIT',
                'course_name' => 'Bachelor of Science in Information Technology',
                'year_level' => 3,
                'email' => 'eleah.carillo@example.com',
                'status' => 'active',
            ],
        ] as $student) {
            Student::updateOrCreate(
                ['student_number' => $student['student_number']],
                $student
            );
        }

        foreach ([
            [
                'course_code' => 'BSIT',
                'semester' => '1st Semester',
                'school_year' => '2026-2027',
                'per_unit_rate' => 350.00,
                'registration_fee' => 500.00,
                'miscellaneous_fee' => 1500.00,
                'laboratory_fee' => 1200.00,
                'other_fee' => 250.00,
                'is_active' => true,
            ],
            [
                'course_code' => 'BSBA',
                'semester' => '1st Semester',
                'school_year' => '2026-2027',
                'per_unit_rate' => 300.00,
                'registration_fee' => 500.00,
                'miscellaneous_fee' => 1200.00,
                'laboratory_fee' => 500.00,
                'other_fee' => 200.00,
                'is_active' => true,
            ],
        ] as $feeSchedule) {
            FeeSchedule::updateOrCreate(
                [
                    'course_code' => $feeSchedule['course_code'],
                    'semester' => $feeSchedule['semester'],
                    'school_year' => $feeSchedule['school_year'],
                ],
                $feeSchedule
            );
        }
    }
}
