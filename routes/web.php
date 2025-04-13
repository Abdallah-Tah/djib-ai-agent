<?php

use Illuminate\Support\Facades\Route;

Route::get('/chatbot', function () {
    return view('ai-agent::livewire.chatbot');
});