<?php

namespace Djib\AiAgent\Services;

use Djib\AiAgent\Models\Document;
use Illuminate\Support\Facades\Log;
use Djib\AiAgent\Interfaces\EmbeddingsInterface;
use Djib\AiAgent\Interfaces\VectorStoreInterface;

class SupabaseService
{
    protected $embeddingModel;
    protected $vectorStore;

    public function __construct(
        EmbeddingsInterface $embeddingModel,
        VectorStoreInterface $vectorStore
    ) {
        $this->embeddingModel = $embeddingModel;
        $this->vectorStore = $vectorStore;
    }

    /**
     * Search for relevant chunks based on a question
     *
     * @param string $question
     * @param int|null $tenantId
     * @return array
     */
    public function searchRelevantChunks(string $question, ?int $tenantId = null): array
    {
        try {
            // Generate embedding for the question
            $embedding = $this->embeddingModel->embed($question);

            if ($embedding === null) {
                Log::error('Failed to generate embedding for question', [
                    'question' => $question
                ]);
                return [];
            }

            // Build filter
            $filter = [];
            if ($tenantId !== null) {
                $filter['tenant_id'] = $tenantId;
            }

            // Perform similarity search
            $results = $this->vectorStore->similaritySearch($embedding, 5, $filter);

            // Format results
            return array_map(function ($doc) {
                return [
                    'content' => $doc->pageContent,
                    'score' => $doc->metadata()['score'] ?? 0
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
     * Store documents in the vector database
     *
     * @param array $documents Array of Document objects
     * @return bool
     */
    public function storeDocuments(array $documents): bool
    {
        try {
            $this->vectorStore->addDocuments($documents);
            return true;
        } catch (\Exception $e) {
            Log::error('Supabase batch store via Prism failed', [
                'exception' => $e->getMessage(),
                'document_count' => count($documents),
                'first_doc_metadata' => $documents[0]->metadata()
            ]);
            return false;
        }
    }
}