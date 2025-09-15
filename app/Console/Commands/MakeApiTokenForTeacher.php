<?php

namespace App\Console\Commands;

use App\Models\Teacher;
use Illuminate\Console\Command;

class MakeApiTokenForTeacher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-api-token-for-teacher {teacherId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make api token for teacher marks sending integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $teacherId = (int)$this->argument('teacherId');

        $user = Teacher::find($teacherId)->user()->first();

        $token = $user->createToken('notification_integration')->plainTextToken;

        $this->info('Token created for teacher ' . $user->login);
        $this->info($token);
    }
}
