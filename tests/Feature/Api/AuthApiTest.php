<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Tests for Auth API endpoints.
 * Note: These are read-only tests that don't modify the database.
 */
class AuthApiTest extends TestCase
{
    #[Test]
    public function it_validates_registration_data(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function it_rejects_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nonexistent-test@email.com',
            'password' => 'wrongpassword123',
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_rejects_unauthenticated_requests_to_me(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }

    #[Test]
    public function it_rejects_unauthenticated_favorites(): void
    {
        $response = $this->getJson('/api/v1/favorites');

        $response->assertStatus(401);
    }
}
