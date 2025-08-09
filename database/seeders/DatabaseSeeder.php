<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Task;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $baseUser = User::factory()->create([
            'login' => 'BaseUser',
            'password' => 'password',
            'role' => 'teacher'
        ]);
        Teacher::factory()->create([
            'user_id' => $baseUser
        ]);

        $testStudent = User::factory()->create([
            'login' => 'TestStudent',
            'password' => 'password',
            'role' => 'student'
        ]);
        Student::factory()->create([
            'user_id' => $testStudent,
            'teacher_id' => $baseUser
        ]);

        Task::factory()->createMany(10);
    }
}
