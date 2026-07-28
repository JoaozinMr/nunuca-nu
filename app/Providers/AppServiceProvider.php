<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\TelegramService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // TelegramService — singleton: one instance per request/job lifecycle
        $this->app->singleton(TelegramService::class, function (): TelegramService {
            return new TelegramService(
                botToken: (string) config('services.telegram.bot_token', ''),
                chatId:   (string) config('services.telegram.chat_id', ''),
            );
        });

        // OrderService — singleton: stateless, safe to reuse across the request
        $this->app->singleton(\App\Services\OrderService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Disable {data: ...} wrapping for all API Resources used as Inertia props.
        // When used as Inertia page props, resources should return flat arrays.
        \Illuminate\Http\Resources\Json\JsonResource::withoutWrapping();
    }
}
