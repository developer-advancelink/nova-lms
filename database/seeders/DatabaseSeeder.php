<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Schedule;
use App\Models\Message;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // -------------------------------------------------------
        // USERS
        // -------------------------------------------------------
        $lecturer = User::firstOrCreate(
            ['email' => 'rashid@university.edu'],
            [
                'name'     => 'Dr. Rashid',
                'password' => Hash::make('password'),
                'role'     => 'lecturer',
            ]
        );

        $student = User::firstOrCreate(
            ['email' => 'zuhairah@university.edu'],
            [
                'name'     => 'Ampuan Zuhairah',
                'password' => Hash::make('password'),
                'role'     => 'student',
            ]
        );

        // -------------------------------------------------------
        // COURSES
        // -------------------------------------------------------
        $cs101 = Course::firstOrCreate(
            ['code' => 'CS101'],
            [
                'name'        => 'Web Development',
                'lecturer_id' => $lecturer->id,
                'start_date'  => '2026-05-20',
                'end_date'    => '2026-12-20',
            ]
        );

        $mgmt101 = Course::firstOrCreate(
            ['code' => 'MGMT101'],
            [
                'name'        => 'Management for Logistics',
                'lecturer_id' => $lecturer->id,
                'start_date'  => '2026-05-20',
                'end_date'    => '2026-12-20',
            ]
        );

        // -------------------------------------------------------
        // ENROLLMENTS
        // -------------------------------------------------------
        Enrollment::firstOrCreate(['user_id' => $student->id, 'course_id' => $cs101->id]);
        Enrollment::firstOrCreate(['user_id' => $student->id, 'course_id' => $mgmt101->id]);

        // -------------------------------------------------------
        // ASSIGNMENTS
        // -------------------------------------------------------
        $assignment1 = Assignment::firstOrCreate(
            ['title' => 'Assignment 1 — Database Design', 'course_id' => $cs101->id],
            [
                'description' => 'Design an ER diagram for a university system.',
                'due_date'    => now()->subDays(14),
            ]
        );

        $assignment2 = Assignment::firstOrCreate(
            ['title' => 'Assignment 2 — ER Diagram', 'course_id' => $cs101->id],
            [
                'description' => 'Submit your ER diagram with normalization notes.',
                'due_date'    => now()->endOfDay(),
            ]
        );

        $assignment3 = Assignment::firstOrCreate(
            ['title' => 'Read Chapter 5', 'course_id' => $mgmt101->id],
            [
                'description' => 'Read Chapter 5 of the course textbook.',
                'due_date'    => now()->addDays(3),
            ]
        );

        $quiz1 = Assignment::firstOrCreate(
            ['title' => 'Quiz 1', 'course_id' => $cs101->id],
            [
                'description' => 'Quiz covering weeks 1-3.',
                'due_date'    => now()->subDays(7),
            ]
        );

        // -------------------------------------------------------
        // SUBMISSIONS
        // -------------------------------------------------------
        Submission::firstOrCreate(
            ['assignment_id' => $assignment1->id, 'user_id' => $student->id],
            [
                'grade'        => 'A-',
                'feedback'     => 'Good structure, improve normalization.',
                'submitted_at' => now()->subDays(13),
            ]
        );

        Submission::firstOrCreate(
            ['assignment_id' => $quiz1->id, 'user_id' => $student->id],
            [
                'grade'        => 'B+',
                'feedback'     => 'Strong understanding.',
                'submitted_at' => now()->subDays(6),
            ]
        );

        // -------------------------------------------------------
        // SCHEDULES
        // -------------------------------------------------------
        Schedule::firstOrCreate(
            ['course_id' => $cs101->id, 'day' => 'Monday'],
            [
                'title' => 'Web Development Class',
                'time'  => '10:00:00',
            ]
        );

        Schedule::firstOrCreate(
            ['course_id' => $mgmt101->id, 'day' => 'Wednesday'],
            [
                'title' => 'Database Lecture',
                'time'  => '14:00:00',
            ]
        );

        // -------------------------------------------------------
        // MESSAGES
        // -------------------------------------------------------

        // Thread 1 — Lecturer sends reminder, student replies
        $thread1 = Message::firstOrCreate(
            [
                'sender_id'   => $lecturer->id,
                'receiver_id' => $student->id,
                'subject'     => 'Re: Assignment 2 Submission',
            ],
            [
                'course_id'  => $cs101->id,
                'parent_id'  => null,
                'body'       => 'Hi Ampuan Zuhairah, just a reminder that for Assignment 2, please make sure you include the ER diagram alongside your written report. The submission portal closes at 11:59 PM tonight. Let me know if you have any questions.',
                'read_at'    => null,
                'created_at' => now()->subHours(3),
            ]
        );

        Message::firstOrCreate(
            [
                'sender_id'   => $student->id,
                'receiver_id' => $lecturer->id,
                'parent_id'   => $thread1->id,
                'subject'     => 'Re: Assignment 2 Submission',
            ],
            [
                'course_id'  => $cs101->id,
                'body'       => 'Thank you for the reminder, Dr. Rashid! I have already prepared the ER diagram and will be attaching it with my submission later today.',
                'read_at'    => now()->subHours(2),
                'created_at' => now()->subHours(2),
            ]
        );

        // Thread 2 — Lecturer sends midterm reminder
        Message::firstOrCreate(
            [
                'sender_id'   => $lecturer->id,
                'receiver_id' => $student->id,
                'subject'     => 'Midterm Exam Schedule Reminder',
            ],
            [
                'course_id'  => $mgmt101->id,
                'parent_id'  => null,
                'body'       => 'Dear students, the midterm exam will be held on the 3rd week of July. Please review chapters 1 through 6. Good luck!',
                'read_at'    => now()->subDay(),
                'created_at' => now()->subDay(),
            ]
        );

        // Thread 3 — Quiz results
        Message::firstOrCreate(
            [
                'sender_id'   => $lecturer->id,
                'receiver_id' => $student->id,
                'subject'     => 'Quiz 1 Results are now available',
            ],
            [
                'course_id'  => $cs101->id,
                'parent_id'  => null,
                'body'       => 'Hi, your Quiz 1 results are now available. You may view your results in the gradebook. Please reach out if you have any concerns.',
                'read_at'    => now()->subDays(3),
                'created_at' => now()->subDays(5),
            ]
        );
    }
}