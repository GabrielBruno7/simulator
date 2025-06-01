<?php

namespace Tests;

use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function postJson(string $uri, array $data = [], array $headers = [])
    {
        $defaultHeaders = ['CONTENT_TYPE' => 'application/json'];
        $mergedHeaders = array_merge($defaultHeaders, $headers);

        return $this->call(
            'POST',
            $uri,
            [],
            [],
            [],
            $mergedHeaders,
            json_encode($data)
        );
    }
}
