<?php
namespace Djib\AiAgent\Services;

use Djib\AiAgent\Models\Document;
use Illuminate\Support\Facades\Log;
use Djib\AiAgent\Interfaces\EmbeddingsInterface;

class SupabaseService
{
    protected EmbeddingsInterface $embeddingModel;

    public function __construct(EmbeddingsInterface $embeddingModel)
    {
        $this->embeddingModel = $embeddingModel;
    }

    /**
     * Search relevant chunks using only the embedding model.
     * This is now a stub and returns an empty array.
     */
    public function searchRelevantChunks(string $question, ?int $tenantId = null): array
    {
        Log::warning('searchRelevantChunks is not implemented: VectorStoreInterface dependency removed.');
        return [];
    }

    /**
     * Store multiple documents. This is now a stub and always returns true.
     */
    public function storeDocuments(array $documents): bool
    {
        Log::warning('storeDocuments is not implemented: VectorStoreInterface dependency removed.');
        return true;
    }

    /**
     * Deprecated stub for storeChunk.
     */
    public function storeChunk(string $content, ?int $tenantId, array $embedding): void
    {
        Log::warning('storeChunk is deprecated and not implemented: VectorStoreInterface dependency removed.');
    }
}
