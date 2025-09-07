<?php

declare(strict_types=1);

namespace App\Services;

interface ChatClientInterface
{
    /**
     * Отправка сообщений в AI сервис
     *
     * @param array<int,array{role:string,content:string}> $messages
     * @param array<string,mixed> $opts
     * @return array<string,mixed>
     */
    public function chat(array $messages, array $opts = []): mixed;

    /**
     * Получение токена доступа
     */
    public function getAccessToken(): string;
}
