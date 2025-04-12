<?php

namespace Djib\AiAgent\Services;

use Prism\VectorStores\Supabase\SupabaseVectorStore;
use Prism\Embeddings\EmbeddingModel;
use Prism\Documents\Document;
use Illuminate\Support\Facades\Log;

class SupabaseService
{
    protected SupabaseVectorStore $vectorStore;
    protected EmbeddingModel $embeddingModel;

    public function __construct(SupabaseVectorStore $vectorStore, EmbeddingModel $embeddingModel)
    {
        $this->vectorStore = $vectorStore;
        $this->embeddingModel = $embeddingModel;
    }

    /**
     * Search relevant chunks using Prism's VectorStore abstraction.
     */
    public function searchRelevantChunks(string $question, ?int $tenantId = null): array
    {
        try {
            $questionEmbedding = $this->embeddingModel->embed($question);

            if (!$questionEmbedding) {
                Log::error('Failed to generate embedding for question', ['question' => $question]);
                return [];
            }

            $k = 5;
            $filter = $tenantId !== null ? ['tenant_id' => $tenantId] : [];

            $results = $this->vectorStore->similaritySearch(
                embedding: $questionEmbedding,
                k: $k,
                filter: $filter
            );

            return array_map(function (Document $doc) {
                return [
                    'content' => $doc->pageContent(),
                    'score' => $doc->metadata()['score'] ?? null
                ];
            }, $results);

        } catch (\Exception $e) {
            Log::error('Supabase search via Prism failed', [
                'exception' => $e->getMessage(),
                'question' => $question,
                'tenantId' => $tenantId
            ]);
            return [];
        }
    }

    /**
     * Store multiple documents using Prism's VectorStore abstraction.
     * Assumes the injected EmbeddingModel will be used by the VectorStore.
     *
     * @param Document[] $documents Array of Prism Document objects
     */
    public function storeDocuments(array $documents): void
    {
        if (empty($documents)) {
            return;
        }

        try {
            // This method should handle embedding generation using the configured EmbeddingModel
            $this->vectorStore->addDocuments($documents);

        } catch (\Exception $e) {
            // Log details about the batch failure
            Log::error('Supabase batch store via Prism failed', [
                'exception' => $e->getMessage(),
                'document_count' => count($documents),
                'first_doc_metadata' => !empty($documents) ? $documents[0]->metadata() : null,
            ]);
            // Optionally re-throw or handle more gracefully
        }
    }

    /**
     * Store a chunk using Prism's VectorStore abstraction.
     * @deprecated Use storeDocuments for better integration with Prism flow.
     *             Kept for compatibility if needed, but ideally removed.
     */
    public function storeChunk(string $content, ?int $tenantId, array $embedding): void
    {
        Log::warning('storeChunk is deprecated. Use storeDocuments instead.');
        try {
            $document = new Document(
                pageContent: $content,
                metadata: ['tenant_id' => $tenantId]
            );
            // This assumes addDocuments can handle pre-embedded vectors if needed,
            // or you might need a different vectorStore method like addVectors.
            // Ideally, let addDocuments handle the embedding.
            $this->vectorStore->addDocuments([$document]);

        } catch (\Exception $e) {
            Log::error('Supabase store via Prism failed (using deprecated storeChunk)', [
                'exception' => $e->getMessage(),
                'content_start' => substr($content, 0, 50),
                'tenantId' => $tenantId
            ]);
        }
    }
}
