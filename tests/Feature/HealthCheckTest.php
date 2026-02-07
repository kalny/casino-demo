<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function testHomePage(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
