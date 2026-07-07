<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
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
            ['name' => 'Admin User', 'email' => 'admin@example.com', 'role' => 'Administrator'],
            ['name' => 'Registrar User', 'email' => 'registrar@example.com', 'role' => 'Registrar'],
            ['name' => 'Staff User', 'email' => 'staff@example.com', 'role' => 'Staff'],
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

        $course = Course::updateOrCreate(
            ['course_code' => 'BSIT'],
            [
                'course_name' => 'Bachelor of Science in Information Technology',
                'department' => 'College of Computer Studies',
                'year_level' => 4,
            ]
        );

        $otherCourse = Course::updateOrCreate(
            ['course_code' => 'BSBA'],
            [
                'course_name' => 'Bachelor of Science in Business Administration',
                'department' => 'College of Business',
                'year_level' => 4,
            ]
        );

        foreach ([
            [
                'student_number' => '2026-0001',
                'first_name' => 'Mark',
                'middle_name' => null,
                'last_name' => 'Fillartos',
                'gender' => 'Male',
                'email' => 'mark.fillartos@example.com',
            ],
            [
                'student_number' => '2026-0002',
                'first_name' => 'Eleah Camille V.',
                'middle_name' => null,
                'last_name' => 'Carillo',
                'gender' => 'Female',
                'email' => 'eleah.carillo@example.com',
            ],
            [
                'student_number' => '2026-0003',
                'first_name' => 'Kenrick',
                'middle_name' => null,
                'last_name' => 'Saballo',
                'gender' => 'Male',
                'email' => 'kenrick.saballo@example.com',
            ],
        ] as $student) {
            Student::updateOrCreate(
                ['student_number' => $student['student_number']],
                $student + [
                    'phone' => '09'.fake()->numerify('########'),
                    'address' => fake()->address(),
                    'birth_date' => now()->subYears(20)->subDays(rand(1, 365)),
                ]
            );
        }

        foreach ([
            ['course_id' => $course->id, 'subject_code' => 'IT101', 'subject_name' => 'Introduction to Computing', 'units' => 3, 'semester' => '1st Semester'],
            ['course_id' => $course->id, 'subject_code' => 'IT102', 'subject_name' => 'Programming Fundamentals', 'units' => 4, 'semester' => '1st Semester'],
            ['course_id' => $course->id, 'subject_code' => 'IT201', 'subject_name' => 'Data Structures', 'units' => 4, 'semester' => '2nd Semester'],
            ['course_id' => $otherCourse->id, 'subject_code' => 'BA101', 'subject_name' => 'Principles of Management', 'units' => 3, 'semester' => '1st Semester'],
        ] as $subject) {
            Subject::updateOrCreate(
                ['subject_code' => $subject['subject_code']],
                $subject
            );
        }
    }
}
