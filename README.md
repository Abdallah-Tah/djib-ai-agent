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

## Prerequisites

Before installing this package, ensure you have:

1. A Supabase project with pgvector extension enabled
2. An OpenAI API key
3. Laravel 10.x or higher
4. PHP 8.1 or higher
5. Livewire 3.x installed in your project

## Installation

1. **Require the package:**
    ```bash
    composer require djib/ai-agent:dev-main
    ```
    *(Adjust `dev-main` if you are using a tagged release)*

2. **Publish Assets:** 
    ```bash
    php artisan vendor:publish --provider="Djib\AiAgent\AiAgentServiceProvider"
    ```
    This will publish:
    * `config/ai-agent.php` (Configuration)
    * `resources/views/vendor/ai-agent/` (Livewire component view)
    * `app/Mail/EscalationAlert.php` (Mail stub)

    You can also publish specific groups using tags:
    ```bash
    php artisan vendor:publish --tag="ai-agent-config"  # Config only
    php artisan vendor:publish --tag="ai-agent-views"   # Views only
    php artisan vendor:publish --tag="ai-agent-mail"    # Mail stub only
    ```

## Configuration

1. **Environment Variables:**
   Add these variables to your `.env` file:

    ```dotenv
    # OpenAI Configuration
    OPENAI_API_KEY=your_openai_key_here
    AI_EMBEDDING_MODEL=text-embedding-3-small

    # Supabase Configuration
    SUPABASE_API_KEY=your_supabase_service_role_key
    SUPABASE_URL=https://your-project-ref.supabase.co
    SUPABASE_VECTOR_TABLE=documents
    SUPABASE_VECTOR_FUNCTION=match_documents

    # Optional Configuration
    AI_LLM_MODEL=gpt-4
    ESCALATION_EMAIL_ADDRESS=your-support@example.com
    ```

2. **Supabase Setup:**
   Create the vector table in your Supabase database:

    ```sql
    -- Enable the pgvector extension
    create extension if not exists vector;

    -- Create the documents table
    create table documents (
        id bigserial primary key,
        content text,
        embedding vector(1536),
        metadata jsonb default '{}'::jsonb,
        created_at timestamp with time zone default timezone('utc'::text, now())
    );

    -- Create a function to search documents
    create or replace function match_documents(
        query_embedding vector(1536),
        match_threshold float,
        match_count int
    )
    returns table (
        id bigint,
        content text,
        similarity float
    )
    language plpgsql
    as $$
    begin
        return query
        select
            id,
            content,
            1 - (documents.embedding <=> query_embedding) as similarity
        from documents
        where 1 - (documents.embedding <=> query_embedding) > match_threshold
        order by documents.embedding <=> query_embedding
        limit match_count;
    end;
    $$;
    ```

## Usage

### 1. Basic Chat Implementation

Add the chatbot to any Blade view:

```blade
<x-layouts.app>
    <div class="w-full max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl border shadow-lg overflow-hidden h-[600px]">
            @livewire('chatbot')
        </div>
    </div>
</x-layouts.app>
```

Make sure your layout includes Livewire's assets:
```blade
<!DOCTYPE html>
<html>
<head>
    @livewireStyles
</head>
<body>
    {{ $slot }}
    
    @livewireScripts
</body>
</html>
```

### 2. Embedding Documents

To embed your documents for AI context:

```bash
php artisan ai-agent:embed-docs path/to/your/documents
```

### 3. Customizing Bot Behavior

You can extend the base Chatbot component for custom behavior:

```php
namespace App\Livewire;

use Djib\AiAgent\Livewire\Chatbot as BaseComponent;

class CustomChatbot extends BaseComponent
{
    protected function beforeSendMessage($message)
    {
        // Add custom logic before processing message
    }

    protected function afterSendMessage($response)
    {
        // Add custom logic after receiving response
    }
}
```

Then use your custom component:
```blade
@livewire('custom-chatbot')
```

## Testing

```bash
composer test
```

## Security

If you discover any security related issues, please email security@example.com instead of using the issue tracker.

## Credits

- [Your Name](https://github.com/yourusername)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
