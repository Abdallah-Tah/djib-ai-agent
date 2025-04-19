<?php

namespace Djib\AiAgent\Interfaces;

interface VectorStoreInterface
{
    public function similaritySearch(array $embedding, int $limit = 5, array $filter = []): array;
    public function addDocuments(array $documents): bool;
}