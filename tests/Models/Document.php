<?php

namespace Djib\AiAgent\Models;

class Document
{
    public string $pageContent;
    protected array $metadata;

    public function __construct(string $pageContent, array $metadata = [])
    {
        $this->pageContent = $pageContent;
        $this->metadata = $metadata;
    }

    public function pageContent(): string
    {
        return $this->pageContent;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }
}