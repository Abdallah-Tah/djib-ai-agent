<?php

namespace Djib\AiAgent\Commands;

use Prism\Prism\Prism;
use Illuminate\Console\Command;
use Djib\AiAgent\Services\SupabaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class EmbedDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai-agent:embed-docs
                            {--file= : The path to the file to embed, relative to the base path.}
                            {--tenant-id= : Optional tenant ID to associate the embeddings with.}
                            {--chunk-size=1000 : The target size for each text chunk.}
                            {--chunk-overlap=200 : The overlap between consecutive chunks.}';

    protected $description = 'Parse, chunk, and embed document content using PrismPHP.';

    protected SupabaseService $supabaseService;

    public function __construct(SupabaseService $supabaseService)
    {
        parent::__construct();
        $this->supabaseService = $supabaseService;
    }

    public function handle(): void
    {
        $fileOption = $this->option('file');
        $tenantId = $this->option('tenant-id');
        $chunkSize = (int) $this->option('chunk-size');
        $chunkOverlap = (int) $this->option('chunk-overlap');
        $filePath = $fileOption ? base_path($fileOption) : null;

        if (!$filePath || !file_exists($filePath)) {
            $this->error("Missing or invalid file path: " . ($fileOption ?: 'Not provided'));
            return;
        }

        if ($chunkOverlap >= $chunkSize) {
            $this->error("Chunk overlap cannot be greater than or equal to chunk size.");
            return;
        }

        try {
            $text = file_get_contents($filePath);
            if ($text === false) {
                $this->error("Could not read file content: {$filePath}");
                return;
            }

            $parser = new Prism(
                chunkSize: $chunkSize,
                chunkOverlap: $chunkOverlap
            );

            $chunks = $parser->parse($text)->toChunks();

            $totalChunks = count($chunks);
            if ($totalChunks === 0) {
                $this->warn("No text chunks found in the file: {$filePath}");
                return;
            }

            $this->info("Found {$totalChunks} chunks (Size: {$chunkSize}, Overlap: {$chunkOverlap}). Starting embedding and storage process...");
            $progressBar = $this->output->createProgressBar($totalChunks);

            $documentsToStore = [];
            $processedCount = 0;
            foreach ($chunks as $chunk) {
                $processedCount++;
                if (empty(trim($chunk->content))) {
                    Log::debug("Skipping empty chunk.");
                    $totalChunks--;
                    $progressBar->setMaxSteps($totalChunks);
                    continue;
                }

                $documentsToStore[] = [
                    'content' => $chunk->content,
                    'metadata' => ['tenant_id' => $tenantId]
                ];

                if (count($documentsToStore) >= 50 || $processedCount === count($chunks)) {
                    if (!empty($documentsToStore)) {
                        $this->supabaseService->storeDocuments($documentsToStore);
                        $progressBar->advance(count($documentsToStore));
                        $documentsToStore = [];
                    }
                }
            }

            if (!empty($documentsToStore)) {
                $this->supabaseService->storeDocuments($documentsToStore);
                $progressBar->advance(count($documentsToStore));
            }

            $progressBar->finish();
            $this->newLine(2);

            $this->info("✅ Attempted to store {$totalChunks} chunks from {$fileOption}. Check logs for any errors.");

        } catch (Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
            Log::error("Error in EmbedDocs command", ['exception' => $e, 'file' => $filePath]);
        }
    }
}
