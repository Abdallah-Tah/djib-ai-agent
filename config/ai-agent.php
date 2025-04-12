<?php

return [

    'operators' => [
        [
            'name' => 'Abdallah',
            'method' => 'whatsapp',
            'destination' => '+12074097887',
        ],
        [
            'name' => 'Djib-Payroll',
            'method' => 'email',
            'destination' => 'support@djib-payroll.com',
        ],
    ],

    'roles' => [
        'support' => [
            'persona' => 'You are a general customer support assistant. Do not share company data.',
            'llm' => 'openai',
        ],
        'tenant' => [
            'persona' => 'You are a payroll assistant for the authenticated company. Use only tenant-specific knowledge.',
            'llm' => 'ollama',
        ],
    ],

    'llm_keys' => [
        'openai' => [
            'key' => env('OPENAI_API_KEY'),
            'endpoint' => env('OPENAI_EMBEDDING_ENDPOINT', 'https://api.openai.com/v1/embeddings'),
            'model' => env('OPENAI_MODEL', 'gpt-4o'),
        ],
        'ollama' => [
            'endpoint' => env('OLLAMA_URL', 'http://localhost:11434/api/chat'),
            'model' => env('OLLAMA_MODEL', 'llama3'),
        ],
        'gemini' => [
            'key' => env('GEMINI_API_KEY'),
            'endpoint' => env('GEMINI_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent'),
            'model' => env('GEMINI_MODEL', 'gemini-pro'),
        ],
        'anthropic' => [
            'key' => env('ANTHROPIC_API_KEY'),
            'endpoint' => env('ANTHROPIC_ENDPOINT', 'https://api.anthropic.com/v1/messages'),
            'model' => env('ANTHROPIC_MODEL', 'claude-3-opus-20240229'),
        ],
    ],

    'embedding' => [
        'provider' => env('AI_EMBEDDING_PROVIDER', 'openai'), // Example: 'openai'
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('AI_EMBEDDING_MODEL', 'text-embedding-3-small'),
            'organization' => env('OPENAI_ORGANIZATION'), // Optional
        ],
        // Add other providers if needed (e.g., Cohere, HuggingFace)
    ],

    'vector_store' => [
        'provider' => env('AI_VECTOR_STORE_PROVIDER', 'supabase'), // Example: 'supabase'
        'supabase' => [
            'api_key' => env('SUPABASE_API_KEY'), // Service Role Key usually
            'url' => env('SUPABASE_URL'), // Your Supabase project URL
            'table' => env('SUPABASE_VECTOR_TABLE', 'documents'), // Match your DB table
            'function' => env('SUPABASE_VECTOR_FUNCTION', 'match_documents'), // Match your DB function
        ],
        // Add other stores if needed (e.g., Pinecone)
    ],

    'llm' => [
        'provider' => env('AI_LLM_PROVIDER', 'openai'),
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('AI_LLM_MODEL', 'gpt-4o'),
            'organization' => env('OPENAI_ORGANIZATION'), // Optional
        ],
        // Add other LLM providers
    ],

];
