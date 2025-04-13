<?php

use Djib\AiAgent\Services\LLMRouter;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Config::set('ai-agent.llm_keys.openai', [
        'api_key' => 'test-key',
        'model' => 'gpt-4'
    ]);

    Config::set('ai-agent.llm_keys.anthropic', [
        'api_key' => 'test-key',
        'model' => 'claude-3'
    ]);
});

test('can route to OpenAI provider', function () {
    Http::fake([
        'api.openai.com/*' => Http::response([
            'choices' => [
                ['message' => ['content' => 'Test response']]
            ]
        ], 200)
    ]);

    $router = new LLMRouter();
    $question = "Test question";
    $context = [
        'context' => [
            ['content' => 'System message']
        ]
    ];

    $result = $router->callLLM($question, $context, 'openai');
    expect($result)->toBe('Test response');
});

test('can route to another provider', function () {
    Http::fake([
        'api.anthropic.com/*' => Http::response([
            'content' => [
                ['text' => 'Test response']
            ]
        ], 200)
    ]);

    $router = new LLMRouter();
    $question = "Test question";
    $context = [
        'context' => [
            ['content' => 'System message']
        ]
    ];

    $result = $router->callLLM($question, $context, 'anthropic');
    expect($result)->toBe('Test response');
});

test('throws exception for unconfigured provider', function () {
    $router = new LLMRouter();
    $question = "Test question";
    $context = ['context' => []];

    expect(fn() => $router->callLLM($question, $context, 'unknown'))
        ->toThrow(InvalidArgumentException::class, 'Unsupported or unconfigured LLM provider: unknown');
});