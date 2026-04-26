<?php

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});

test('login page loads successfully', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('incendios module requires authentication', function () {
    $response = $this->get('/incendios');

    $response->assertRedirect('/login');
});

test('rescate module requires authentication', function () {
    $response = $this->get('/rescate');

    $response->assertRedirect('/login');
});
