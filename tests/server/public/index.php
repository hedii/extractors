<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$app = new Laravel\Lumen\Application(
    realpath(__DIR__ . '/../')
);

$app->router->get('/get', function () {
    return response('test', 200);
});

$app->router->get('/page1', function () {
    return response(file_get_contents(__DIR__ . '/../views/page1.php'));
});

$app->router->get('/example', function () {
    return response(file_get_contents(__DIR__ . '/../views/example.php'));
});

$app->router->get('/links', function () {
    return response(file_get_contents(__DIR__ . '/../views/links.php'));
});

$app->router->get('/emails', function () {
    return response(file_get_contents(__DIR__ . '/../views/emails.php'));
});

$app->run();
