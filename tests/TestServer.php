<?php

namespace Hedii\Extractors\Test;

class TestServer
{
    static function start()
    {
        $pid = exec('php -S ' . 'localhost:' . getenv('TEST_SERVER_PORT') . ' -t ./tests/server/public > /dev/null 2>&1 & echo $!');

        while (@file_get_contents('http://localhost:' . getenv('TEST_SERVER_PORT') . '/get') === false) {
            usleep(1000);
        }

        register_shutdown_function(function () use ($pid) {
            exec('kill ' . $pid);
        });
    }
}
