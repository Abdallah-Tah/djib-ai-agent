<?php

namespace Djib\AiAgent\Services;

use App\Helpers\GlobalFunctions;
use Illuminate\Support\Facades\Log;

class TenantAwareResponder
{
    public function respond(string $question, string $role = 'tenant'): ?string
    {
        $tenantId = null;

        if ($role === 'tenant' && auth()->check()) {
            $tenantId = GlobalFunctions::getAuthenticatedUserCompany()->id;
        }

        $persona = config("ai-agent.roles.{$role}.persona", "You are a helpful assistant.");

        // Retrieve relevant documents from Supabase with tenant filtering
        $documents = app(SupabaseService::class)->searchRelevantChunks($question, $tenantId);

        $context = [
            'version' => 'v1',
            'context' => [
                [
                    'type' => 'text',
                    'content' => $persona,
                    'source' => 'system'
                ],
            ],
        ];

        foreach ($documents as $doc) {
            $context['context'][] = [
                'type' => 'doc',
                'content' => $doc['content'],
                'source' => 'supabase'
            ];
        }

        // Get LLM provider from config
        $provider = config("ai-agent.roles.{$role}.llm", 'openai');

        // Call LLM and return the response
        return app(LLMRouter::class)->callLLM($question, $context, $provider);
    }
}
