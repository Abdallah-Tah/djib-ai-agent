<?php

namespace Djib\AiAgent;

use Livewire\Livewire;
use Djib\AiAgent\Livewire\Chatbot;
use Djib\AiAgent\Commands\EmbedDocs;
use Illuminate\Support\ServiceProvider;
use Djib\AiAgent\Services\SupabaseService;
use Djib\AiAgent\Services\EmbeddingService;
use Djib\AiAgent\Interfaces\EmbeddingsInterface;
use Djib\AiAgent\Interfaces\VectorStoreInterface;

class AiAgentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ai-agent.php', 'ai-agent');

        $this->app->bind(EmbeddingsInterface::class, EmbeddingService::class);
        $this->app->bind(VectorStoreInterface::class);
        $this->app->singleton(SupabaseService::class);
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ai-agent');

        if (class_exists(Livewire::class)) {
            Livewire::component('ai-agent::chatbot', Chatbot::class);
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                EmbedDocs::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/ai-agent.php' => config_path('ai-agent.php'),
            ], 'ai-agent-config');

            $this->publishes([
                __DIR__ . '/../stubs/Mail/EscalationAlert.php' => app_path('Mail/EscalationAlert.php'),
            ], 'ai-agent-mail');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/ai-agent'),
            ], 'ai-agent-views');
        }
    }

    public function provides()
    {
        return [
            EmbeddingsInterface::class,
            SupabaseService::class,
        ];
    }

    public function shouldDiscoverEvents()
    {
        return true;
    }

    public function shouldDiscoverRoutes()
    {
        return true;
    }

    public function shouldDiscoverViews()
    {
        return true;
    }

    public function shouldDiscoverTranslations()
    {
        return true;
    }
}
