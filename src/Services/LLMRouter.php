<?php

namespace Djib\AiAgent\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LLMRouter
{
    public function callLLM(string $question, array $context, string $provider = 'openai'): ?string
    {
        // Ensure the provider exists in the config to avoid errors later
        if (!config("ai-agent.llm_keys.{$provider}")) {
            throw new \InvalidArgumentException("Unsupported or unconfigured LLM provider: {$provider}");
        }

        return match ($provider) {
            'openai' => $this->callOpenAI($question, $context),
            'anthropic' => $this->callClaude($question, $context),
            'gemini' => $this->callGemini($question, $context),
            'ollama' => $this->callOllama($question, $context),
            default => throw new \InvalidArgumentException("Unsupported LLM provider: {$provider}"), // Should be caught by the check above, but good practice
        };
    }

    protected function callOpenAI(string $question, array $context): ?string
    {
        $config = config('ai-agent.llm_keys.openai');
        $response = Http::withToken($config['key']) // Use key from config
            ->post($config['endpoint'], [ // Use endpoint from config
                'model' => $config['model'], // Use model from config
                'messages' => [
                    ['role' => 'system', 'content' => json_encode($context)], // Consider if json_encode is the right format for system context
                    ['role' => 'user', 'content' => $question]
                ],
                'temperature' => 0.7, // These could also be moved to config if needed
                'max_tokens' => 1024,
                'top_p' => 1,
                'frequency_penalty' => 0,
            ]);

        return $response->successful() ? $response['choices'][0]['message']['content'] : null;
    }

    protected function callClaude(string $question, array $context): ?string
    {
        $config = config('ai-agent.llm_keys.anthropic');
        $response = Http::withHeaders([ // Anthropic uses headers for API key
            'x-api-key' => $config['key'],
            'anthropic-version' => '2023-06-01' // Recommended header
        ])
            ->post($config['endpoint'], [ // Use endpoint from config
                'model' => $config['model'], // Use model from config
                'messages' => [
                    ['role' => 'user', 'content' => $question]
                ],
                // Ensure context structure matches what Claude expects for 'system'
                'system' => $context['context'][0]['content'] ?? '',
                'max_tokens' => 1024 // Could be moved to config
            ]);

        return $response->successful() ? $response['content'][0]['text'] ?? null : null;
    }

    protected function callGemini(string $question, array $context): ?string
    {
        $config = config('ai-agent.llm_keys.gemini');
        $endpoint = $config['endpoint'] . '?key=' . $config['key']; // Append key to endpoint URL

        $response = Http::post($endpoint, [
            'contents' => [
                // Ensure context structure matches what Gemini expects
                ['parts' => [['text' => ($context['context'][0]['content'] ?? '') . "\n" . $question]]]
            ]
            // Add generationConfig if needed (temperature, max_tokens etc.)
            // 'generationConfig' => [
            //     'temperature' => 0.7,
            //     'maxOutputTokens' => 1024,
            // ]
        ]);

        // Check for potential errors in the response body even if status is 200
        if (!$response->successful() || isset($response['error'])) {
            Log::error('Gemini API Error: ' . $response->body());
            return null;
        }

        return $response['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }

    protected function callOllama(string $question, array $context): ?string
    {
        $config = config('ai-agent.llm_keys.ollama');
        $response = Http::post($config['endpoint'], [ // Use endpoint from config
            'model' => $config['model'], // Use model from config
            'messages' => [
                // Ensure context structure matches what Ollama expects
                ['role' => 'system', 'content' => $context['context'][0]['content'] ?? ''],
                ['role' => 'user', 'content' => $question]
            ],
            'stream' => false // Explicitly set stream to false unless you handle streaming
        ]);

        return $response->successful() ? $response['message']['content'] ?? null : null;
    }
}
