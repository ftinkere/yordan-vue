<?php

namespace Tests\Feature\Code200;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRegister200Test extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
    }
}
