<?php

namespace Djib\AiAgent\Interfaces;

interface EmbeddingsInterface
{
    public function embed(string $text): ?array;
}