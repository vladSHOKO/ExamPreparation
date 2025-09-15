<?php

use App\Models\TaskSession;
use App\Models\User;
use Database\Factories\StudentFactory;
use Database\Factories\TaskFactory;
use Database\Factories\TaskSessionFactory;
use Database\Factories\TeacherFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiMarksTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->getMarksUrl = route('api.marks', ['TestClass']);

        $userStudent = UserFactory::new()->create(['login' => 'testStudent', 'password' => 'test', 'role' => 'student']
        );
        $userTeacher = UserFactory::new()->create(['login' => 'testTeacher', 'password' => 'test', 'role' => 'teacher']
        );
        $teacher = TeacherFactory::new()->create(['user_id' => $userTeacher->id]);
        $student = StudentFactory::new()->create(
            ['user_id' => $userStudent->id, 'teacher_id' => $teacher->id, 'class_number' => 'TestClass']
        );
        $tasks = TaskFactory::new()->createMany(10);

        foreach ($tasks->take(5) as $task) {
            TaskSessionFactory::new()->create([
                'student_id' => $student->id,
                'status' => 'processing',
                'task_id' => $task->id
            ]);
        }

        foreach ($tasks->slice(5, 5) as $task) {
            TaskSessionFactory::new()->create([
                'student_id' => $student->id,
                'status' => 'completed',
                'task_id' => $task->id
            ]);
        }

        $this->userStudent = $userStudent;
        $this->student = $student;
        $this->tasks = $tasks;
        $this->teacher = $teacher;
    }

    public function test_user_without_right_api_key_cannot_be_accessed()
    {
        $response = $this->getJson($this->getMarksUrl);

        $response->assertStatus(401);
    }

    public function test_user_with_right_api_key_can_be_accessed()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson($this->getMarksUrl);

        $response->assertStatus(200);
    }

    public function test_response_returns_correct_json_structure()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson($this->getMarksUrl);

        $response->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'student_id',
                    'login',
                    'completed_tasks',
                    'teacher_id',
                    'completion_percentage',
                    'last_activity'
                ]
            ]
        ]);
    }

    public function test_response_returns_correct_data()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson($this->getMarksUrl);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    [
                        'student_id' => $this->student->id,
                        'login' => $this->userStudent->login,
                        'completed_tasks' => TaskSession::query()
                            ->where(['student_id' => $this->student->id, 'status' => 'completed'])
                            ->pluck('task_id')
                            ->toArray(),
                        'teacher_id' => $this->teacher->id,
                        'completion_percentage' => 50,
                        'last_activity' => TaskSession::query()
                            ->where('student_id', $this->student->id)
                            ->latest('updated_at')
                            ->value('updated_at')?->timestamp,
                    ]
                ]
            ]);
    }
}
