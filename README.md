# Djib AI Agent for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/djib/ai-agent.svg?style=flat-square)](https://packagist.org/packages/djib/ai-agent)
[![Total Downloads](https://img.shields.io/packagist/dt/djib/ai-agent.svg?style=flat-square)](https://packagist.org/packages/djib/ai-agent)

This package provides tools and services to integrate AI capabilities into your Laravel application, including:

*   A Livewire Chatbot component.
*   Document embedding and vector search using Supabase/pgvector.
*   Tenant-aware context for chatbot responses and document retrieval.
*   Configurable embedding models (via PrismPHP, defaults to OpenAI).
*   An Artisan command for embedding documents.

## Features

*   **Livewire Chatbot:** A ready-to-use chat interface component.
*   **Tenant-Aware Responses:** Chatbot context changes based on user authentication status.
*   **Vector Search:** Embed documents and perform similarity searches using `SupabaseService`.
*   **Document Embedding Command:** Use `php artisan ai-agent:embed-docs` to process and store documents.
*   **Configurable:** Set API keys, models, and Supabase details via environment variables.
*   **Extensible:** Core services are bound to interfaces for easy customization.

## Installation

1.  **Require the package:**
    ```bash
    composer require djib/ai-agent:dev-main
    ```
    *(Adjust `dev-main` if you are using a tagged release)*

2.  **Publish Assets:** This step copies the necessary configuration file, views, and mail stubs to your application.
    ```bash
    php artisan vendor:publish --provider="Djib\AiAgent\AiAgentServiceProvider"
    ```
    *   This will publish:
        *   `config/ai-agent.php` (Configuration)
        *   `resources/views/vendor/ai-agent/` (Livewire component view)
        *   `app/Mail/EscalationAlert.php` (Mail stub)
    *   You can also publish specific groups using tags: `--tag="ai-agent-config"`, `--tag="ai-agent-views"`, `--tag="ai-agent-mail"`.

### 1. Livewire Chatbot Component

Embed the chatbot component in any of your Blade views using the package's namespace:

```blade
{{-- Example: resources/views/dashboard.blade.php --}}

<x-layouts.app>
    <h1>Chatbot</h1>

    {{-- Add the Livewire component using the namespace --}}
    @livewire('ai-agent::chatbot')

</x-layouts.app>
```

Make sure your layout includes `@livewireStyles` in the `<head>` and `@livewireScripts` before the closing `</body>` tag.

## Configuration

After publishing, configure the package by editing your `.env` file. Add the necessary API keys and Supabase details based on the published `config/ai-agent.php` file.

```dotenv
# --- Djib AI Agent Configuration ---

# Embedding Model (Using PrismPHP's OpenAI integration)
# AI_EMBEDDING_PROVIDER=openai # Currently defaults to openai via PrismPHP
OPENAI_API_KEY=sk-... # Your OpenAI API Key
# OPENAI_ORGANIZATION=org-... # Optional
# AI_EMBEDDING_MODEL=text-embedding-3-small # Or your preferred OpenAI embedding model

# Vector Store (Using PrismPHP's Supabase integration)
# AI_VECTOR_STORE_PROVIDER=supabase # Currently defaults to supabase via PrismPHP
SUPABASE_API_KEY=your_supabase_service_role_key # Important: Use Service Role Key
SUPABASE_URL=https://your-project-ref.supabase.co
# SUPABASE_VECTOR_TABLE=documents # Your table name in Supabase with pgvector enabled
# SUPABASE_VECTOR_FUNCTION=match_documents # Your Supabase RPC function for vector search

# LLM (Optional - If using LLM features via PrismPHP)
# AI_LLM_PROVIDER=openai
# AI_LLM_MODEL=gpt-4o # Or your preferred OpenAI chat/completion model

# Escalation Email
# ESCALATION_EMAIL_ADDRESS=your-support-email@example.com

# --- End Djib AI Agent Configuration ---