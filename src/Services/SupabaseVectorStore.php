<?php

namespace Djib\AiAgent\Services;

use Djib\AiAgent\Interfaces\VectorStoreInterface;
use Djib\AiAgent\Models\Document;

class SupabaseVectorStore implements VectorStoreInterface
{
    public function similaritySearch(array $embedding, int $limit = 5, array $filter = []): array
    {
        // Dummy implementation for now
        return [];
    }

    public function addDocuments(array $documents): bool
    {
        // Dummy implementation for now
        return true;
    }
}
