<?php

namespace Tests\Middleware;

use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    public function test_success_with_auth_token()
    {
        $this->get('get-data', $this->headers())
            ->assertSuccessful()
            ->assertJsonStructure([
                'data',
                'message',
                'success'
            ])
            ->assertJson([
                'data' => "Data",
                'success' => true,
                'message' => "Identity works correctly"
            ]);
    }

    public function test_fail_without_auth_token()
    {
        $this->get('get-data', $this->headers(false))
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'success'
            ])
            ->assertJson([
                'message' => 'Unauthorized',
                'success' => false
            ]);
    }

    public function test_fail_with_wrong_auth_token()
    {
        $headers = $this->headers(false);
        $headers['Authorization'] = "Wrong Token";

        $this->get('get-data', $headers)
            ->assertSuccessful()
            ->assertJsonStructure([
                'message',
                'success'
            ])
            ->assertJson([
                'message' => 'Unauthorized',
                'success' => false
            ]);
    }
}