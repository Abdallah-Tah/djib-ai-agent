<?php

namespace Djib\AiAgent\Tests;

use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            \Djib\AiAgent\AiAgentServiceProvider::class
        ];
    }
}
