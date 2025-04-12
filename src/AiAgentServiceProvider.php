<?php

namespace Djib\AiAgent;

use Illuminate\Support\ServiceProvider;

class AiAgentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ai-agent.php', 'ai-agent');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'ai-agent');

    }

    public function boot()
    {
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
