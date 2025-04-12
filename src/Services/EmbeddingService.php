<?php

namespace Djib\AiAgent\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmbeddingService
{
    public function embed(string $text): ?array
    {
        $response = Http::withToken(
            config('ai-agent.llm_keys.openai')
        )->post(
                config('ai-agent.llm_keys.openai.endpoint'),
                [
                    'input' => $text,
                    'model' => 'text-embedding-3-small',
                    'encoding_format' => 'float',
                ]
            );

        if (!$response->successful()) {
            Log::error('OpenAI embedding request failed', ['body' => $response->body()]);
            return null;
        }

        return $response->json('data.0.embedding') ?? null;
    }
}