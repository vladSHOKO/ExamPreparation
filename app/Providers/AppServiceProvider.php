<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\ChatClientInterface;
use App\Services\OpenAIChatClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Регистрируем OpenAIChatClient как реализацию ChatClientInterface
        $this->app->bind(ChatClientInterface::class, OpenAIChatClient::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
