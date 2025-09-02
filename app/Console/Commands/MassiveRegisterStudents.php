<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MassiveRegisterStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:massive-register-students {file} {teacherUserID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register a lot of students. Reading data from file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        $teacher = Teacher::where('user_id', $this->argument('teacherUserID'))->first();

        if (!$teacher) {
            $this->error('Teacher not found');
            return 1;
        }

        if (!file_exists($filePath)) {
            $this->error('File does not exist');
            return 1;
        }

        $users = json_decode(file_get_contents($filePath), true);

        if (!$users) {
            $this->error('Failed to parse json file');
            return 1;
        }

        foreach ($users as $userData) {
            $user = new User();
            $user->login = $userData['username'];
            $user->password = Hash::make($userData['password']);
            $user->role = 'student';
            $user->save();

            $student = new Student();
            $student->user()->associate($user);
            $student->teacher()->associate($teacher);
            $student->save();

            $this->info("Student {$userData['username']} successfully registered");
        }

        return 0;
    }
}
