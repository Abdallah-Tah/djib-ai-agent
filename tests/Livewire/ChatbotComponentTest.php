<?php

namespace Djib\AiAgent\Tests\Livewire;

use Djib\AiAgent\Livewire\Chatbot;
use Djib\AiAgent\Services\TenantAwareResponder;
use Djib\AiAgent\Tests\TestCase; // Import the base TestCase
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Livewire\Livewire;
use Illuminate\Foundation\Auth\User;
use Mockery;

// Use the TestCase to ensure Laravel is bootstrapped
uses(TestCase::class);

test('can send message as unauthenticated user', function () {
    $responder = Mockery::mock(TenantAwareResponder::class);
    // Use swap instead of instance
    $this->swap(TenantAwareResponder::class, $responder);

    // Mock the Auth facade check
    Auth::shouldReceive('check')->once()->andReturn(false);

    $responder->shouldReceive('respond')
        ->with('Hello', 'support')
        ->once()
        ->andReturn('Bot response');

    Livewire::test(Chatbot::class)
        ->set('message', 'Hello')
        ->call('send')
        ->assertSet('message', '')
        ->assertSet('conversation', [
            ['user' => 'Hello'],
            ['bot' => 'Bot response']
        ]);
});

test('can send message as authenticated user', function () {
    $user = new User();
    // Ensure actingAs is available
    $this->actingAs($user);

    $responder = Mockery::mock(TenantAwareResponder::class);
    // Use swap
    $this->swap(TenantAwareResponder::class, $responder);

    // Mock the Auth facade check
    Auth::shouldReceive('check')->once()->andReturn(true);

    $responder->shouldReceive('respond')
        ->with('Hello', 'tenant')
        ->once()
        ->andReturn('Tenant bot response');

    Livewire::test(Chatbot::class)
        ->set('message', 'Hello')
        ->call('send')
        ->assertSet('message', '')
        ->assertSet('conversation', [
            ['user' => 'Hello'],
            ['bot' => 'Tenant bot response']
        ]);
});

test('maintains conversation history', function () {
    $responder = Mockery::mock(TenantAwareResponder::class);
    // Use swap
    $this->swap(TenantAwareResponder::class, $responder);

    // Mock Auth facade check (assuming unauthenticated for this test)
    Auth::shouldReceive('check')->twice()->andReturn(false);

    $responder->shouldReceive('respond')
        ->with('First message', 'support')
        ->once()
        ->andReturn('First response');

    $responder->shouldReceive('respond')
        ->with('Second message', 'support')
        ->once()
        ->andReturn('Second response');

    $component = Livewire::test(Chatbot::class)
        ->set('message', 'First message')
        ->call('send')
        ->assertSet('conversation', [
            ['user' => 'First message'],
            ['bot' => 'First response']
        ]);

    $component->set('message', 'Second message')
        ->call('send')
        ->assertSet('conversation', [
            ['user' => 'First message'],
            ['bot' => 'First response'],
            ['user' => 'Second message'],
            ['bot' => 'Second response']
        ]);
});