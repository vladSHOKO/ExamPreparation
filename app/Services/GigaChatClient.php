<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GigaChatClient
{
    private array $config;

    public function __construct(
    ) {
        $this->config = config('services.gigachat');
    }

    private function getTokenCacheKey(): string
    {
        $scope = $this->config['scope'];
        return "gigachat:token:{$scope}";
    }

    public function clearToken(): void
    {
        Cache::forget($this->getTokenCacheKey());
    }

    public function getAccessToken(): string
    {
        return Cache::remember($this->getTokenCacheKey(), 30, function () {
            $url   = rtrim($this->config['base_url_for_access_token'], '/').($this->config['token_path'] ?? '/api/v2/oauth');
            $id    = $this->config['client_id'];
            $secret= $this->config['client_secret'];
            $scope = $this->config['scope'];

            $resp = Http::withOptions([
                'verify' => false,
            ])->withHeaders([
                // Некоторые стенды требуют RqUID, некоторые X-Request-ID — добавим оба:
                'RqUID'        => (string) Str::uuid(),
                'X-Request-ID' => (string) Str::uuid(),
                'Authorization'=> 'Basic ' . $secret,
            ])
                ->timeout($this->cfg['timeout'] ?? 30)
                ->asForm()
                ->post($url, [
                    'scope' => $scope,
                    'grant_type' => 'client_credentials',
                    ])
                ->throw();

            $data = $resp->json();

            $token = $data['access_token'] ?? null;
            if (!$token) throw new \RuntimeException('GigaChat: пустой access_token');

            // TTL
            $ttl = 50; // default buffer
            if (isset($data['expires_in'])) {
                $ttl = max(30, (int)$data['expires_in'] - 60);
            } elseif (isset($data['expires_at'])) {
                $ttl = max(30, Carbon::parse($data['expires_at'])->diffInSeconds(now()) - 60);
            }
            Cache::put($this->getTokenCacheKey(), $token, $ttl);

            return $token;
        });
    }

    /**
     * @param array<int,array{role:string,content:string}> $messages
     * @return array<string,mixed>|\Psr\Http\Message\StreamInterface
     */
    public function chat(array $messages, array $opts = []): mixed
    {
        $url = rtrim($this->config['base_url_for_message_sending'], '/').($this->config['chat_path'] ?? '/api/v1/chat/completions');

        $payload = array_filter([
            'model'       => $this->config['model'] ?? 'gigachat:latest',
            'messages'    => $messages,
            'temperature' => $opts['temperature'] ?? 0.3,
            'top_p'       => $opts['top_p'] ?? 0.9,
            'max_tokens'  => $opts['max_tokens'] ?? 800,
        ], fn($v) => $v !== null);

        $headers = [
            'Authorization' => 'Bearer '.$this->getAccessToken(),
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'X-Request-ID'  => (string) Str::uuid(),
        ];

        $http = Http::withOptions([
            'verify' => false,
        ])->withHeaders($headers)->timeout($this->config['timeout'] ?? 30);

        try {
            $resp = $http->post($url, $payload);
            if ($resp->status() === 401 || $resp->status() === 403) {
                $this->clearToken();
                $headers['Authorization'] = 'Bearer '.$this->getAccessToken();
                $resp = Http::withHeaders($headers)->timeout($this->config['timeout'] ?? 30)->post($url, $payload);
            }
            $resp->throw();

            return $resp->json();
        } catch (RequestException $e) {
            // 429: можно читать Retry-After
            if (optional($e->response())->status() === 429) {
                $retry = (int) ($e->response()->header('Retry-After') ?? 1);
                throw new \RuntimeException("GigaChat: перегрузка, попробуйте через {$retry} сек.");
            }
            throw $e;
        }
    }
}
