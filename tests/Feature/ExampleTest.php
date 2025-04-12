<?php

it('verifies a feature of the application', function () {
    $response = $this->get('/some-feature-endpoint');

    $response->assertStatus(200);
});