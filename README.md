# Djib AI Agent for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/djib/ai-agent.svg?style=flat-square)](https://packagist.org/packages/djib/ai-agent)
[![Total Downloads](https://img.shields.io/packagist/dt/djib/ai-agent.svg?style=flat-square)](https://packagist.org/packages/djib/ai-agent)

This package provides tools and services to integrate AI capabilities, such as document embedding, vector search, and potentially LLM interactions, into your Laravel application. It leverages the [PrismPHP](https://prismphp.com/) library for interacting with embedding models, vector stores (specifically Supabase/pgvector), and text splitting.

## Features

*   Embed document content into a vector store.
*   Perform similarity searches against embedded documents.
*   Tenant-aware document storage and retrieval.
*   Configurable text chunking using PrismPHP.
*   Uses Supabase (via `pgvector`) as the vector store backend.

## Installation

You can install the package via Composer:

```bash
composer require djib/ai-agent
```

## Configuration

To configure the AI Agent, add the following environment variables to your `.env` file:

```dotenv
# --- AI Agent Configuration ---

# Embedding Model (Using PrismPHP's OpenAI integration)
AI_EMBEDDING_PROVIDER=openai
OPENAI_API_KEY=sk-...
OPENAI_ORGANIZATION=org-... # Optional
AI_EMBEDDING_MODEL=text-embedding-3-small # Or your preferred OpenAI embedding model

# Vector Store (Using PrismPHP's Supabase integration)
AI_VECTOR_STORE_PROVIDER=supabase
SUPABASE_API_KEY=your_supabase_service_role_key # Important: Use Service Role Key
SUPABASE_URL=https://your-project-ref.supabase.co
SUPABASE_VECTOR_TABLE=documents # Your table name in Supabase with pgvector enabled
SUPABASE_VECTOR_FUNCTION=match_documents # Your Supabase RPC function for vector search

# LLM (Optional - If using LLM features via PrismPHP)
AI_LLM_PROVIDER=openai
AI_LLM_MODEL=gpt-4o # Or your preferred OpenAI chat/completion model

# --- End AI Agent Configuration ---
```

## Example Usage

Below is an example of how you can use the AI Agent in your Laravel application to perform a search:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Djib\AiAgent\Services\SupabaseService;

class SearchController extends Controller
{
    protected SupabaseService $supabaseService;

    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    public function search(Request $request)
    {
        $query = $request->input('query', 'Default search query');
        $tenantId = $request->user()->company_id; // Example: Get tenant ID from authenticated user

        // Perform the search
        $results = $this->supabaseService->searchRelevantChunks($query, $tenantId);

        // $results will be an array like:
        // [
        //   ['content' => 'Relevant text chunk 1...', 'score' => 0.85],
        //   ['content' => 'Relevant text chunk 2...', 'score' => 0.82],
        //   ...
        // ]

        return view('search.results', ['results' => $results]);
    }
}
```

## Next Steps

1.  **Save:** Save this content as `README.md` in the root of your `djib-ai-agent` package directory (`/home/abdallah-mohamed/laravel-apps/packages/djib-ai-agent/README.md`).
2.  **License File:** If you don't have one, create a `LICENSE.md` file with the MIT license text (you can easily find templates online).
3.  **Review:** Read through the generated README and adjust any details specific to your package's implementation (e.g., if you have different service names, additional features, or specific Supabase setup instructions).
4.  **Commit:** Add the `README.md` and `LICENSE.md` to your Git repository.