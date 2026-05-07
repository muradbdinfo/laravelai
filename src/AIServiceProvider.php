<?php
namespace EasyAI\LaravelAI;
use Illuminate\Support\ServiceProvider;
use EasyAI\LaravelAI\RAG\RAGManager;
use EasyAI\LaravelAI\Console\RagIngestCommand;

class AIServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ai.php', 'ai');
        $this->app->singleton('laravel-ai', function ($app) {
            return new AIManager($app);
        });
        $this->app->alias('laravel-ai', AIManager::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Moved here so config() is available
        $this->app->singleton(RAGManager::class, fn() => new RAGManager());

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/ai.php' => config_path('ai.php'),
            ], 'ai-config');
            $this->commands([
                RagIngestCommand::class,
            ]);
        }
    }
}