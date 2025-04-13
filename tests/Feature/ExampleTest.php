<?php

test('chatbot component renders', function () {
    $response = $this->get('/chatbot');
    $response->assertOk();
});