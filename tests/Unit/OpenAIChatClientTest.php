<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\OpenAIChatClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenAIChatClientTest extends TestCase
{
    use RefreshDatabase;

    private OpenAIChatClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Настраиваем конфигурацию для тестов
        config([
            'services.openai' => [
                'api_key' => 'test-api-key',
                'model' => 'gpt-5-mini',
                'base_url' => 'https://api.openai.com/v1',
                'timeout' => '30', // Тестируем строковое значение
                'max_tokens' => 800,
                'max_completion_tokens' => 800,
                'temperature' => 1.0,
                'verify_ssl' => false, // Отключаем SSL для тестов
            ]
        ]);

        $this->client = new OpenAIChatClient();
    }

    public function test_get_access_token_returns_api_key(): void
    {
        $token = $this->client->getAccessToken();
        
        $this->assertEquals('test-api-key', $token);
    }

    public function test_get_access_token_throws_exception_when_api_key_missing(): void
    {
        config(['services.openai.api_key' => null]);
        
        // Создаем новый экземпляр клиента с обновленной конфигурацией
        $client = new OpenAIChatClient();
        
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('OpenAI API key is not configured');
        
        $client->getAccessToken();
    }

    public function test_chat_sends_correct_request(): void
    {
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Test response'
                        ]
                    ]
                ]
            ], 200)
        ]);

        $messages = [
            ['role' => 'user', 'content' => 'Hello']
        ];

        $response = $this->client->chat($messages);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.openai.com/v1/chat/completions' &&
                   $request->hasHeader('Authorization', 'Bearer test-api-key') &&
                   $request->hasHeader('Content-Type', 'application/json') &&
                   $request['model'] === 'gpt-5-mini' &&
                   $request['messages'] === [['role' => 'user', 'content' => 'Hello']] &&
                   $request['max_completion_tokens'] === 800 &&
                   $request['temperature'] === 1.0 &&
                   !isset($request['top_p']) && // GPT-5 mini не поддерживает top_p
                   !isset($request['frequency_penalty']) && // GPT-5 mini не поддерживает frequency_penalty
                   !isset($request['presence_penalty']); // GPT-5 mini не поддерживает presence_penalty
        });

        $this->assertEquals('Test response', $response['choices'][0]['message']['content']);
    }

    public function test_chat_handles_401_error(): void
    {
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([], 401)
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('OpenAI: Invalid API key');

        $this->client->chat([['role' => 'user', 'content' => 'Hello']]);
    }

    public function test_chat_handles_429_rate_limit(): void
    {
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([], 429, [
                'Retry-After' => '60'
            ])
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('OpenAI: Rate limit exceeded. Retry after 60 seconds');

        $this->client->chat([['role' => 'user', 'content' => 'Hello']]);
    }

    public function test_clear_token_does_nothing(): void
    {
        // Метод должен выполняться без ошибок
        $this->client->clearToken();
        
        $this->assertTrue(true); // Если мы дошли до этой строки, значит метод работает
    }

    public function test_chat_handles_connection_timeout(): void
    {
        Http::fake([
            'api.openai.com/v1/chat/completions' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('cURL error 28: Resolving timed out after 10001 milliseconds');
            }
        ]);

        $this->expectException(\Illuminate\Http\Client\ConnectionException::class);
        $this->expectExceptionMessage('cURL error 28: Resolving timed out after 10001 milliseconds');

        $this->client->chat([['role' => 'user', 'content' => 'Hello']]);
    }

    public function test_chat_auto_corrects_temperature_for_gpt5_mini(): void
    {
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Test response'
                        ]
                    ]
                ]
            ], 200)
        ]);

        // Устанавливаем temperature = 0.0 в конфигурации
        config(['services.openai.temperature' => 0.0]);

        $messages = [
            ['role' => 'user', 'content' => 'Hello']
        ];

        $this->client->chat($messages);

        Http::assertSent(function ($request) {
            return $request['temperature'] === 1.0; // Должно быть автоматически исправлено на 1.0
        });
    }

    public function test_chat_sends_all_params_for_non_gpt5_mini(): void
    {
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Test response'
                        ]
                    ]
                ]
            ], 200)
        ]);

        // Создаем новый клиент с моделью gpt-4
        config(['services.openai.model' => 'gpt-4']);
        $client = new OpenAIChatClient();

        $messages = [
            ['role' => 'user', 'content' => 'Hello']
        ];

        $client->chat($messages);

        Http::assertSent(function ($request) {
            return $request['model'] === 'gpt-4' &&
                   isset($request['top_p']) && // Другие модели поддерживают top_p
                   isset($request['frequency_penalty']) && // Другие модели поддерживают frequency_penalty
                   isset($request['presence_penalty']); // Другие модели поддерживают presence_penalty
        });

        // Возвращаем обратно gpt-5-mini для других тестов
        config(['services.openai.model' => 'gpt-5-mini']);
    }
}
