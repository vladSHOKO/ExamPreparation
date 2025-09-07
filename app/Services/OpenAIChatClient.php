<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OpenAIChatClient implements ChatClientInterface
{
    private array $config;

    public function __construct()
    {
        $this->config = config('services.openai');
    }

    /**
     * OpenAI использует API ключ вместо OAuth токенов
     * Возвращаем API ключ напрямую
     */
    public function getAccessToken(): string
    {
        $apiKey = $this->config['api_key'] ?? null;

        if (!$apiKey) {
            throw new \RuntimeException('OpenAI API key is not configured');
        }

        return $apiKey;
    }

    /**
     * Отправка сообщений в OpenAI API
     *
     * @param array<int,array{role:string,content:string}> $messages
     * @param array<string,mixed> $opts
     * @return array<string,mixed>
     * @throws RequestException
     */
    public function chat(array $messages, array $opts = []): mixed
    {
        set_time_limit(300);

        $url = rtrim($this->config['base_url'], '/') . '/chat/completions';

        // Получаем значения параметров
        $temperature = $opts['temperature'] ?? $this->config['temperature'] ?? 1.0;
        $maxTokens = $opts['max_completion_tokens'] ?? $opts['max_tokens'] ?? $this->config['max_completion_tokens'] ?? $this->config['max_tokens'] ?? 800;
        $topP = $opts['top_p'] ?? $this->config['top_p'] ?? 0.5;
        $frequencyPenalty = $opts['frequency_penalty'] ?? $this->config['frequency_penalty'] ?? 0.0;
        $presencePenalty = $opts['presence_penalty'] ?? $this->config['presence_penalty'] ?? 0.0;

        // Для GPT-5 mini temperature должен быть 1.0 (значение по умолчанию)
        $model = $this->config['model'] ?? 'gpt-5-mini';
        if (str_contains($model, 'gpt-5-mini') && (float)$temperature === 0.0) {
            $temperature = 1.0;
        }

        // Базовые параметры для всех моделей
        $payload = [
            'model' => $model,
            'messages' => $messages,
        ];

        // Добавляем параметры в зависимости от модели
        if (str_contains($model, 'gpt-5-mini')) {
            // GPT-5 mini поддерживает только ограниченный набор параметров
            $payload['temperature'] = (float) $temperature;
            $payload['max_completion_tokens'] = (int) $maxTokens;
            // Не добавляем top_p, frequency_penalty, presence_penalty для GPT-5 mini
        } else {
            // Для других моделей добавляем все параметры
            $payload['temperature'] = (float) $temperature;
            $payload['max_completion_tokens'] = (int) $maxTokens;
            $payload['top_p'] = (float) $topP;
            $payload['frequency_penalty'] = (float) $frequencyPenalty;
            $payload['presence_penalty'] = (float) $presencePenalty;
        }

        // Убираем null значения
        $payload = array_filter($payload, fn($v) => $v !== null);

        $headers = [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2', // Для поддержки новых функций
        ];

        $httpOptions = [
            'timeout' => (int) ($this->config['timeout'] ?? 300), // Увеличиваем таймаут до 5 минут
            'connect_timeout' => (int) ($this->config['connect_timeout'] ?? 60), // Увеличиваем таймаут подключения до 1 минуты
        ];

        // В Docker окружении или при проблемах с SSL можно отключить проверку сертификатов
        $verifySsl = $this->config['verify_ssl'] ?? true;

        // Преобразуем строковые значения в boolean
        if (is_string($verifySsl)) {
            $verifySsl = filter_var($verifySsl, FILTER_VALIDATE_BOOLEAN);
        }

        // Настройки cURL для улучшения подключения
        $curlOptions = [
            CURLOPT_DNS_CACHE_TIMEOUT => 300, // Кеширование DNS на 5 минут
            CURLOPT_TCP_KEEPALIVE => 1,
            CURLOPT_TCP_KEEPIDLE => 10,
            CURLOPT_TCP_KEEPINTVL => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
        ];

        if (!$verifySsl) {
            // Отключаем проверку SSL сертификатов
            $httpOptions['verify'] = false;
            $curlOptions[CURLOPT_SSL_VERIFYPEER] = false;
            $curlOptions[CURLOPT_SSL_VERIFYHOST] = false;
        } else {
            $httpOptions['verify'] = true;
        }

        $httpOptions['curl'] = $curlOptions;

        $http = Http::withHeaders($headers)
            ->withOptions($httpOptions);

        // Retry логика для случаев таймаута
        $maxRetries = 3;
        $retryDelay = 5; // секунд

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = $http->post($url, $payload);

                // Обработка ошибок аутентификации
                if ($response->status() === 401) {
                    throw new \RuntimeException('OpenAI: Invalid API key');
                }

                if ($response->status() === 403) {
                    throw new \RuntimeException('OpenAI: Access forbidden - check your API key permissions');
                }

                if ($response->status() === 429) {
                    $retryAfter = $response->header('Retry-After');
                    throw new \RuntimeException("OpenAI: Rate limit exceeded. Retry after {$retryAfter} seconds");
                }

                $response->throw();

                // Если запрос успешен, выходим из цикла retry
                break;

            } catch (ConnectionException $e) {
                if (str_contains($e->getMessage(), 'timed out') && $attempt < $maxRetries) {
                    \Log::warning("OpenAI request timed out on attempt {$attempt}, retrying in {$retryDelay} seconds");
                    sleep($retryDelay);
                    continue;
                }
                throw $e; // Если это последняя попытка или другая ошибка, пробрасываем исключение
            }
        }

        try {
            $data = $response->json();

            // Проверяем структуру ответа
            if (!isset($data['choices'][0]['message'])) {
                throw new \RuntimeException('OpenAI: Invalid response format');
            }

            // Проверяем, что content не пустой
            $content = $data['choices'][0]['message']['content'] ?? '';
            if (empty(trim($content))) {
                \Log::warning('OpenAI returned empty content', [
                    'response' => $data,
                    'request' => $payload
                ]);

                // Если ответ пустой, попробуем увеличить max_completion_tokens и повторить запрос
                if (isset($payload['max_completion_tokens']) && $payload['max_completion_tokens'] < 4000) {
                    \Log::info('Retrying with increased max_completion_tokens');
                    $payload['max_completion_tokens'] = 4000;

                    $retryResponse = $http->post($url, $payload);
                    $retryResponse->throw();
                    $retryData = $retryResponse->json();

                    \Log::info('OpenAI Retry Response:', $retryData);

                    if (!empty(trim($retryData['choices'][0]['message']['content'] ?? ''))) {
                        return $retryData;
                    }
                }
            }

            return $data;

        } catch (RequestException $e) {
            $response = $e->response;

            if ($response) {
                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? $e->getMessage();

                throw new \RuntimeException("OpenAI API error: {$errorMessage}", $e->getCode(), $e);
            }

            throw $e;
        } catch (ConnectionException $e) {
            // Специальная обработка для ошибок таймаута
            if (str_contains($e->getMessage(), 'timed out')) {
                \Log::warning('OpenAI request timed out', [
                    'error' => $e->getMessage(),
                    'payload' => $payload
                ]);
                throw new ConnectionException("OpenAI connection timeout: The request took too long to complete. Please check your internet connection and try again.", $e->getCode(), $e);
            }

            throw new ConnectionException("OpenAI connection error: {$e->getMessage()}", $e->getCode(), $e);
        } catch (\Exception $e) {
            // Обработка других ошибок, включая превышение времени выполнения
            if (str_contains($e->getMessage(), 'Maximum execution time')) {
                \Log::error('PHP execution time exceeded', [
                    'error' => $e->getMessage(),
                    'payload' => $payload
                ]);
                throw new \RuntimeException("Request timeout: The operation took too long to complete. Please try again with a simpler request.", $e->getCode(), $e);
            }

            throw $e;
        }
    }
}
