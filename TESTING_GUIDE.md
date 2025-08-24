# 🧪 Руководство по тестированию Laravel-приложения ExamPreparation

## 📋 Summary

Данный документ содержит полное руководство по тестированию Laravel-приложения ExamPreparation - системы подготовки к экзаменам с интеграцией GigaChat AI. 

**Ключевые выводы анализа:**
- Проект требует **100-120 тестов** для полного покрытия
- Критически важны тесты аутентификации, проверки ответов и ролевой системы
- Рекомендуемое покрытие кода: **85-90%**
- Время выполнения всех тестов: **< 3 минут**

**Архитектура тестирования:**
- **Unit Tests** (40 тестов) - модели, сервисы, валидация
- **Feature Tests** (60 тестов) - HTTP-запросы, интеграция компонентов
- **Integration Tests** (20 тестов) - внешние API, файловые операции

---

## 🛠️ Настройка тестовой среды

### 1. Базовая конфигурация

Laravel уже настроен для тестирования. Проверьте файл `phpunit.xml`:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>app</directory>
        </include>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="CACHE_STORE" value="array"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
    </php>
</phpunit>
```

### 2. Создание тестовой базы данных

**SQLite in-memory** (уже настроено):
- Быстрая работа
- Изоляция тестов
- Автоматическая очистка

**Альтернативно - отдельная БД:**
```bash
# Создание тестовой БД
php artisan migrate --env=testing
```

### 3. Настройка .env.testing

```env
APP_ENV=testing
APP_DEBUG=true
DB_CONNECTION=sqlite
DB_DATABASE=:memory:

# Отключаем внешние сервисы для тестов
GIGACHAT_CLIENT_ID=test_client_id
GIGACHAT_CLIENT_SECRET=test_secret
GIGACHAT_BASE_URL=http://mock-gigachat.test

# Ускоряем хеширование паролей
BCRYPT_ROUNDS=4
```

### 4. Подготовка фабрик и сидеров

**Создание фабрик для моделей:**

```bash
php artisan make:factory UserFactory
php artisan make:factory StudentFactory
php artisan make:factory TeacherFactory
php artisan make:factory TaskFactory
php artisan make:factory TaskSessionFactory
```

---

## 🏗️ Основные функции тестирования Laravel

### 1. Базовый класс TestCase

```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase; // Очищает БД после каждого теста
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Общие настройки для всех тестов
        $this->withoutExceptionHandling(); // Показывает полные ошибки
    }
}
```

### 2. Ключевые трейты Laravel

**RefreshDatabase** - пересоздает БД для каждого теста:
```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
}
```

**DatabaseTransactions** - откатывает транзакции (быстрее):
```php
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;
}
```

**WithFaker** - генерация тестовых данных:
```php
use Illuminate\Foundation\Testing\WithFaker;

class ExampleTest extends TestCase
{
    use WithFaker;
    
    public function test_example()
    {
        $name = $this->faker->name;
        $email = $this->faker->email;
    }
}
```

### 3. HTTP-тестирование

**Основные методы:**
```php
// GET запросы
$response = $this->get('/tasks');
$response = $this->getJson('/api/tasks');

// POST запросы
$response = $this->post('/login', ['login' => 'user', 'password' => 'pass']);
$response = $this->postJson('/api/tasks', $data);

// Аутентификация
$user = User::factory()->create();
$response = $this->actingAs($user)->get('/dashboard');

// Проверка ответов
$response->assertStatus(200);
$response->assertRedirect('/dashboard');
$response->assertJson(['success' => true]);
$response->assertViewIs('tasks.collection');
```

### 4. Тестирование базы данных

```php
// Проверка существования записей
$this->assertDatabaseHas('users', ['login' => 'testuser']);
$this->assertDatabaseMissing('users', ['login' => 'deleted']);

// Подсчет записей
$this->assertDatabaseCount('tasks', 5);

// Мягкое удаление
$this->assertSoftDeleted('users', ['id' => 1]);
```

### 5. Моки и заглушки

```php
// Мок внешнего сервиса
$mock = $this->mock(GigaChatClient::class);
$mock->shouldReceive('chat')
     ->once()
     ->with($messages)
     ->andReturn(['choices' => [['message' => ['content' => 'Test response']]]]);

// Мок фасада
Mail::fake();
Mail::assertSent(WelcomeMail::class);

// Мок файловой системы
Storage::fake('public');
Storage::assertExists('tasks/test-file.pdf');
```

---

## 📝 Примеры тестов для проекта ExamPreparation

### 1. Unit Test - Модель Student

```php
<?php

namespace Tests\Unit\Models;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Tests\TestCase;

class StudentTest extends TestCase
{
    public function test_student_belongs_to_user()
    {
        // Arrange - подготовка данных
        $user = User::factory()->create();
        $teacher = Teacher::factory()->create();
        $student = Student::factory()->create([
            'user_id' => $user->id,
            'teacher_id' => $teacher->id
        ]);

        // Act & Assert - действие и проверка
        $this->assertInstanceOf(User::class, $student->user);
        $this->assertEquals($user->id, $student->user->id);
    }

    public function test_student_belongs_to_teacher()
    {
        $teacher = Teacher::factory()->create();
        $student = Student::factory()->create(['teacher_id' => $teacher->id]);

        $this->assertInstanceOf(Teacher::class, $student->teacher);
        $this->assertEquals($teacher->id, $student->teacher->id);
    }

    public function test_student_has_many_task_sessions()
    {
        $student = Student::factory()->create();
        
        // Создаем связанные TaskSession через фабрику
        TaskSession::factory()->count(3)->create(['student_id' => $student->id]);

        $this->assertCount(3, $student->tasks);
    }
}
```

### 2. Feature Test - Аутентификация

```php
<?php

namespace Tests\Feature\Auth;

use App\Models\Teacher;
use App\Models\User;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function test_user_can_login_with_valid_credentials()
    {
        // Arrange
        $user = User::factory()->create([
            'login' => 'testuser',
            'password' => bcrypt('password123')
        ]);

        // Act
        $response = $this->post('/login', [
            'login' => 'testuser',
            'password' => 'password123'
        ]);

        // Assert
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'login' => 'testuser',
            'password' => bcrypt('password123')
        ]);

        $response = $this->post('/login', [
            'login' => 'testuser',
            'password' => 'wrongpassword'
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['login']);
        $this->assertGuest();
    }

    public function test_student_registration_creates_user_and_student()
    {
        // Arrange
        $teacher = Teacher::factory()->create();

        // Act
        $response = $this->post('/register', [
            'login' => 'newstudent',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'teacher_id' => $teacher->id
        ]);

        // Assert
        $response->assertRedirect('/');
        
        // Проверяем создание пользователя
        $this->assertDatabaseHas('users', [
            'login' => 'newstudent'
        ]);
        
        // Проверяем создание студента
        $user = User::where('login', 'newstudent')->first();
        $this->assertDatabaseHas('students', [
            'user_id' => $user->id,
            'teacher_id' => $teacher->id
        ]);
    }
}
```

### 3. Feature Test - Система заданий

```php
<?php

namespace Tests\Feature\Tasks;

use App\Models\Student;
use App\Models\Task;
use App\Models\TaskSession;
use App\Models\User;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    public function test_student_can_view_task_collection()
    {
        // Arrange
        $user = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);
        Task::factory()->count(3)->create();

        // Act
        $response = $this->actingAs($user)->get('/tasks');

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('tasks.collection');
        $response->assertViewHas('result');
    }

    public function test_opening_task_creates_task_session()
    {
        // Arrange
        $user = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create();

        // Act
        $response = $this->actingAs($user)->get("/tasks/{$task->id}");

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('task_sessions', [
            'task_id' => $task->id,
            'student_id' => $student->id
        ]);
    }

    public function test_correct_answer_marks_task_as_completed()
    {
        // Arrange
        $user = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['answer' => 'correct answer']);
        $taskSession = TaskSession::factory()->create([
            'task_id' => $task->id,
            'student_id' => $student->id,
            'status' => 'in_progress'
        ]);

        // Act
        $response = $this->actingAs($user)->post("/tasks/{$task->id}/check", [
            'answer' => 'correct answer'
        ]);

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $taskSession->refresh();
        $this->assertEquals('completed', $taskSession->status);
    }

    public function test_incorrect_answer_shows_error()
    {
        // Arrange
        $user = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['answer' => 'correct answer']);
        TaskSession::factory()->create([
            'task_id' => $task->id,
            'student_id' => $student->id
        ]);

        // Act
        $response = $this->actingAs($user)->post("/tasks/{$task->id}/check", [
            'answer' => 'wrong answer'
        ]);

        // Assert
        $response->assertRedirect();
        $response->assertSessionHasErrors(['answer']);
    }
}
```

### 4. Integration Test - GigaChat

```php
<?php

namespace Tests\Integration;

use App\Models\ChatMessage;
use App\Models\Student;
use App\Models\Task;
use App\Models\TaskSession;
use App\Models\User;
use App\Services\GigaChatClient;
use Tests\TestCase;

class ChatMessagesControllerTest extends TestCase
{
    public function test_posting_message_calls_gigachat_and_saves_response()
    {
        // Arrange
        $user = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create();
        $taskSession = TaskSession::factory()->create([
            'task_id' => $task->id,
            'student_id' => $student->id
        ]);

        // Мокаем GigaChatClient
        $this->mock(GigaChatClient::class, function ($mock) {
            $mock->shouldReceive('chat')
                 ->once()
                 ->andReturn([
                     'choices' => [
                         [
                             'message' => [
                                 'role' => 'assistant',
                                 'content' => 'Это ответ от GigaChat'
                             ]
                         ]
                     ]
                 ]);
        });

        // Act
        $response = $this->actingAs($user)
                         ->postJson("/tasks/{$task->id}/chat", [
                             'content' => 'Помоги с заданием',
                             'role' => 'user'
                         ]);

        // Assert
        $response->assertStatus(200);
        $response->assertJson([
            'role' => 'assistant',
            'content' => 'Это ответ от GigaChat'
        ]);

        // Проверяем сохранение сообщений в БД
        $this->assertDatabaseHas('chat_messages', [
            'task_session_id' => $taskSession->id,
            'content' => 'Помоги с заданием',
            'role' => 'user'
        ]);

        $this->assertDatabaseHas('chat_messages', [
            'task_session_id' => $taskSession->id,
            'content' => 'Это ответ от GigaChat',
            'role' => 'assistant'
        ]);
    }
}
```

### 5. Unit Test - GigaChatClient Service

```php
<?php

namespace Tests\Unit\Services;

use App\Services\GigaChatClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GigaChatClientTest extends TestCase
{
    public function test_get_access_token_returns_cached_token()
    {
        // Arrange
        Cache::put('gigachat:token:GIGACHAT_API_PERS', 'cached_token', 60);
        
        $client = new GigaChatClient();

        // Act
        $token = $client->getAccessToken();

        // Assert
        $this->assertEquals('cached_token', $token);
    }

    public function test_get_access_token_fetches_new_token_when_cache_empty()
    {
        // Arrange
        Http::fake([
            '*/api/v2/oauth' => Http::response([
                'access_token' => 'new_token',
                'expires_in' => 3600
            ])
        ]);

        $client = new GigaChatClient();

        // Act
        $token = $client->getAccessToken();

        // Assert
        $this->assertEquals('new_token', $token);
        Http::assertSent(function ($request) {
            return $request->url() === config('services.gigachat.base_url_for_access_token') . '/api/v2/oauth';
        });
    }

    public function test_chat_sends_correct_payload()
    {
        // Arrange
        Cache::put('gigachat:token:GIGACHAT_API_PERS', 'test_token', 60);
        
        Http::fake([
            '*/api/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Test response'
                        ]
                    ]
                ]
            ])
        ]);

        $client = new GigaChatClient();
        $messages = [
            ['role' => 'user', 'content' => 'Test message']
        ];

        // Act
        $response = $client->chat($messages);

        // Assert
        $this->assertEquals('Test response', $response['choices'][0]['message']['content']);
        
        Http::assertSent(function ($request) use ($messages) {
            $payload = $request->data();
            return $payload['messages'] === $messages &&
                   $payload['model'] === 'gigachat:latest' &&
                   isset($payload['temperature']);
        });
    }
}
```

---

## 🚀 Запуск тестов

### Основные команды

```bash
# Запуск всех тестов
php artisan test

# Запуск конкретного типа тестов
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Запуск конкретного теста
php artisan test tests/Unit/Models/StudentTest.php

# Запуск с покрытием кода
php artisan test --coverage

# Запуск с детальным выводом
php artisan test --verbose

# Параллельный запуск (быстрее)
php artisan test --parallel
```

### Полезные опции

```bash
# Остановка на первой ошибке
php artisan test --stop-on-failure

# Запуск только упавших тестов
php artisan test --retry

# Фильтрация по имени
php artisan test --filter=test_user_can_login

# Группировка тестов
php artisan test --group=auth
```

---

## 📊 Мониторинг качества

### 1. Покрытие кода

```bash
# Генерация отчета покрытия
php artisan test --coverage --min=80

# HTML отчет
php artisan test --coverage-html=coverage-report
```

### 2. Статический анализ

```bash
# Установка PHPStan
composer require --dev phpstan/phpstan

# Анализ кода
./vendor/bin/phpstan analyse app tests
```

### 3. Интеграция с CI/CD

**GitHub Actions (.github/workflows/tests.yml):**

```yaml
name: Tests

on: [push, pull_request]

jobs:
  tests:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: 8.2
        extensions: sqlite3
        
    - name: Install dependencies
      run: composer install --no-progress --prefer-dist --optimize-autoloader
      
    - name: Copy environment file
      run: cp .env.example .env.testing
      
    - name: Generate application key
      run: php artisan key:generate --env=testing
      
    - name: Run tests
      run: php artisan test --coverage --min=80
```

---

## 🎯 Лучшие практики

### 1. Структура тестов

**Принцип AAA (Arrange-Act-Assert):**
```php
public function test_example()
{
    // Arrange - подготовка данных
    $user = User::factory()->create();
    
    // Act - выполнение действия
    $response = $this->actingAs($user)->get('/dashboard');
    
    // Assert - проверка результата
    $response->assertStatus(200);
}
```

### 2. Именование тестов

```php
// ✅ Хорошо - описывает что тестируется
public function test_user_can_login_with_valid_credentials()

// ❌ Плохо - неинформативно
public function test_login()
```

### 3. Изоляция тестов

```php
// ✅ Каждый тест независим
public function test_user_creation()
{
    $user = User::factory()->create(); // Свои данные
    // ...
}

// ❌ Зависимость от других тестов
public function test_user_update()
{
    $user = User::first(); // Может не существовать
    // ...
}
```

### 4. Использование фабрик

```php
// ✅ Фабрики для создания данных
$user = User::factory()->create(['login' => 'testuser']);

// ❌ Ручное создание
$user = new User();
$user->login = 'testuser';
$user->save();
```

---

## 📈 Метрики успеха

### Целевые показатели

- **Покрытие кода:** 85-90%
- **Время выполнения:** < 3 минут
- **Стабильность:** 0 падающих тестов
- **Количество тестов:** 100-120

### Контроль качества

- Все новые фичи покрыты тестами
- PR не мержится без прохождения тестов
- Регулярный рефакторинг тестов
- Мониторинг производительности тестов

---

## 🔧 Troubleshooting

### Частые проблемы

**1. Тесты падают из-за кеша:**
```bash
php artisan config:clear
php artisan cache:clear
```

**2. Проблемы с БД:**
```bash
php artisan migrate:fresh --env=testing
```

**3. Медленные тесты:**
```bash
# Используйте DatabaseTransactions вместо RefreshDatabase
use Illuminate\Foundation\Testing\DatabaseTransactions;
```

**4. Проблемы с файлами:**
```php
// Используйте fake storage
Storage::fake('public');
```

Этот документ обеспечивает полное понимание тестирования Laravel-приложения от базовых концепций до продвинутых техник.
