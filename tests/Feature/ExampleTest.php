<?php

declare(strict_types=1);

it('that the application return a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
