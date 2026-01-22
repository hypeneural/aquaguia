<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Tests for Location API endpoints (states, cities, tags).
 * Uses existing data in database.
 */
class LocationApiTest extends TestCase
{
    #[Test]
    public function it_can_list_states(): void
    {
        $response = $this->getJson('/api/v1/states');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['abbr', 'name']
                ]
            ]);
    }

    #[Test]
    public function it_can_get_cities_by_state(): void
    {
        // Get first state from list
        $statesResponse = $this->getJson('/api/v1/states');
        $states = $statesResponse->json('data');

        if (count($states) > 0) {
            $abbr = $states[0]['abbr'];
            $response = $this->getJson("/api/v1/states/{$abbr}/cities");

            $response->assertStatus(200)
                ->assertJson(['success' => true]);
        } else {
            $this->markTestSkipped('No states in database');
        }
    }

    #[Test]
    public function it_can_list_all_cities(): void
    {
        $response = $this->getJson('/api/v1/cities');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    #[Test]
    public function it_can_list_tags(): void
    {
        $response = $this->getJson('/api/v1/tags');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['slug', 'label']
                ]
            ]);
    }

    #[Test]
    public function states_have_park_count(): void
    {
        $response = $this->getJson('/api/v1/states');

        $response->assertStatus(200);

        $states = $response->json('data');
        if (count($states) > 0) {
            $this->assertArrayHasKey('parkCount', $states[0]);
        }
    }
}
