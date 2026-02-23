<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use App\Models\UserCourseEnrollment;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        // Enroll all employee-type users in the first 3 published courses
        $employees = User::where('user_type', 'employer_user')->get();
        $courses = Course::where('status', 'published')->take(3)->get();

        foreach ($employees as $employee) {
            foreach ($courses as $index => $course) {
                UserCourseEnrollment::firstOrCreate(
                    [
                        'user_id' => $employee->id,
                        'course_id' => $course->id,
                    ],
                    [
                        'enrolled_at' => now()->subDays(rand(1, 30)),
                        'due_at' => now()->addDays(rand(3, 30)),
                        'started_at' => now()->subDays(rand(0, 15)),
                        'progress_percentage' => $index === 0 ? rand(50, 90) : ($index === 1 ? rand(10, 49) : 0),
                        'points_earned' => $index === 0 ? rand(50, 150) : ($index === 1 ? rand(10, 49) : 0),
                    ]
                );
            }
        }

        $this->command->info('Enrolled ' . $employees->count() . ' users in ' . $courses->count() . ' courses.');
    }
}
