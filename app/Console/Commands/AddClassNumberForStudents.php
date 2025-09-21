<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\User;
use Illuminate\Console\Command;

class AddClassNumberForStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-class {class_name} {class_list}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $classListFilePath = $this->argument('class_list');
        $className = $this->argument('class_name');

        if (!file_exists($classListFilePath)) {
            $this->error('File does not exist');
            return 1;
        }

        $users = json_decode(file_get_contents($classListFilePath), true);

        if (!$users) {
            $this->error('Failed to parse json file');
            return 1;
        }

        foreach ($users as $userData) {
            $user = Student::with('user')->whereHas('user', function ($user) use ($userData) {
                $user->where('login', $userData['username']);
            })->firstOrFail();
            $user->class_number = $className;
            $result = $user->save();

            if ($result) {
                $this->info('Class number added for: ' . $userData['username']);
            } else {
                $this->error('Failed to add class for: ' . $userData['username']);
            }
        }

        return 0;
    }
}
