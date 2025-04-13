<?php

namespace Djib\AiAgent\Tests\Unit\Services;

use Mockery as m;
use Djib\AiAgent\Models\Document;
use Djib\AiAgent\Tests\TestCase;
use Illuminate\Support\Facades\Log;
use Djib\AiAgent\Interfaces\EmbeddingsInterface;
use Djib\AiAgent\Services\SupabaseService;
use Djib\AiAgent\Interfaces\VectorStoreInterface;

beforeEach(function () {
    $this->mockEmbeddingModel = m::mock(EmbeddingsInterface::class);
    $this->mockVectorStore = m::mock(VectorStoreInterface::class);

    $this->supabaseService = new SupabaseService(
        $this->mockEmbeddingModel,
        $this->mockVectorStore
    );
});

afterEach(function () {
    m::close();
});

test('searchRelevantChunks returns relevant chunks when embedding is successful', function () {
    // Arrange
    $question = 'Test question';
    $tenantId = 1;
    $mockEmbedding = [0.1, 0.2, 0.3]; // Simple mock vector
    $mockResults = [
        new Document(pageContent: 'Result 1', metadata: ['score' => 0.92]),
        new Document(pageContent: 'Result 2', metadata: ['score' => 0.85])
    ];

    // Mock embedding generation
    $this->mockEmbeddingModel
        ->shouldReceive('embed')
        ->once()
        ->with($question)
        ->andReturn($mockEmbedding);

    // Mock similarity search
    $this->mockVectorStore
        ->shouldReceive('similaritySearch')
        ->once()
        ->with($mockEmbedding, 5, ['tenant_id' => $tenantId])
        ->andReturn($mockResults);

    // Act
    $result = $this->supabaseService->searchRelevantChunks($question, $tenantId);

    // Assert
    expect($result)->toHaveCount(2);
    expect($result[0]['content'])->toBe('Result 1');
    expect($result[0]['score'])->toBe(0.92);
    expect($result[1]['content'])->toBe('Result 2');
    expect($result[1]['score'])->toBe(0.85);
});

test('searchRelevantChunks returns empty array when embedding fails', function () {
    // Arrange
    $question = 'Test question';

    // Mock embedding model to return null (failed embedding)
    $this->mockEmbeddingModel
        ->shouldReceive('embed')
        ->once()
        ->with($question)
        ->andReturn(null);

    // Mock logging of error
    Log::shouldReceive('error')
        ->once()
        ->withArgs(function ($message, $context) use ($question) {
            return $message === 'Failed to generate embedding for question' &&
                $context['question'] === $question;
        });

    // Act
    $result = $this->supabaseService->searchRelevantChunks($question);

    // Assert
    expect($result)->toBeArray()->toBeEmpty();
});

test('searchRelevantChunks logs and returns empty array on exception', function () {
    // Arrange
    $question = 'Test question that causes exception';
    $tenantId = 1;
    $mockEmbedding = [0.1, 0.2, 0.3];
    $exceptionMessage = 'Database connection error';

    // Mock embedding generation
    $this->mockEmbeddingModel
        ->shouldReceive('embed')
        ->once()
        ->with($question)
        ->andReturn($mockEmbedding);

    // Mock similarity search to throw exception
    $this->mockVectorStore
        ->shouldReceive('similaritySearch')
        ->once()
        ->with($mockEmbedding, 5, ['tenant_id' => $tenantId])
        ->andThrow(new \Exception($exceptionMessage));

    // Mock logging of error
    Log::shouldReceive('error')
        ->once()
        ->withArgs(function ($message, $context) use ($question, $tenantId, $exceptionMessage) {
            return $message === 'Supabase search via Prism failed' &&
                $context['exception'] === $exceptionMessage &&
                $context['question'] === $question &&
                $context['tenantId'] === $tenantId;
        });

    // Act
    $result = $this->supabaseService->searchRelevantChunks($question, $tenantId);

    // Assert
    expect($result)->toBeArray()->toBeEmpty();
});

test('storeDocuments stores documents successfully', function () {
    // Arrange: Create mock documents
    $documents = [
        new Document(pageContent: 'Test chunk', metadata: ['tenant_id' => 1])
    ];

    // Mock the vector store's addDocuments method
    $this->mockVectorStore
        ->shouldReceive('addDocuments')
        ->once()
        ->with($documents)
        ->andReturn(true);

    // Act: Call the method under test
    $result = $this->supabaseService->storeDocuments($documents);

    // Assert
    expect($result)->toBeTrue();
});

test('storeDocuments logs error on vector store exception', function () {
    // Arrange: Create mock documents
    $documents = [
        new Document(pageContent: 'Chunk causing error', metadata: ['tenant_id' => 2]),
    ];
    $exceptionMessage = 'Failed to connect to vector store';

    // Mock the vector store's addDocuments method to throw an exception
    $this->mockVectorStore
        ->shouldReceive('addDocuments')
        ->once()
        ->with($documents)
        ->andThrow(new \Exception($exceptionMessage));

    // Mock the Log facade
    Log::shouldReceive('error')
        ->once()
        ->withArgs(function ($message, $context) use ($exceptionMessage, $documents) {
            return str_contains($message, 'Supabase batch store via Prism failed') &&
                $context['exception'] === $exceptionMessage &&
                $context['document_count'] === count($documents) &&
                $context['first_doc_metadata'] === $documents[0]->metadata();
        });

    // Act: Call the method under test
    $result = $this->supabaseService->storeDocuments($documents);

    // Assert
    expect($result)->toBeFalse();
});
