<?php

namespace Tests\Feature;

use Tests\TestCase;

class PingTest extends TestCase
{
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->get('/api/ping')
            ->assertOk()
            ->assertSeeText('pong');
    }
}
