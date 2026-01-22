<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Tests for Park API endpoints.
 * Uses existing data in database - does NOT refresh/truncate tables.
 */
class ParkApiTest extends TestCase
{
    #[Test]
    public function it_can_list_parks(): void
    {
        $response = $this->getJson('/api/v1/parks?limit=5');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'pagination',
            ])
            ->assertJson(['success' => true]);

        $this->assertIsArray($response->json('data'));
    }

    #[Test]
    public function it_can_filter_parks_by_state(): void
    {
        $response = $this->getJson('/api/v1/parks?filter[state]=SP&limit=5');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    #[Test]
    public function it_can_filter_parks_by_max_price(): void
    {
        $response = $this->getJson('/api/v1/parks?filter[maxPrice]=500');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    #[Test]
    public function it_can_filter_parks_by_min_rating(): void
    {
        $response = $this->getJson('/api/v1/parks?filter[min_rating]=0');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    #[Test]
    public function it_can_search_parks(): void
    {
        $response = $this->getJson('/api/v1/parks/search?q=parque');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['success', 'data']);
    }

    #[Test]
    public function it_can_get_parks_home_data(): void
    {
        $response = $this->getJson('/api/v1/parks/home');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => ['featuredCities', 'topParks', 'stats']
            ]);
    }

    #[Test]
    public function it_can_use_cursor_pagination(): void
    {
        $response = $this->getJson('/api/v1/parks/cursor?limit=5');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'data',
                'pagination' => ['limit', 'has_more']
            ]);
    }

    #[Test]
    public function it_can_use_field_selection(): void
    {
        $response = $this->getJson('/api/v1/parks/cursor?limit=5&fields=id,name,slug');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    #[Test]
    public function it_includes_pagination_metadata(): void
    {
        $response = $this->getJson('/api/v1/parks?withFilters=true');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'pagination' => ['page', 'limit', 'total', 'totalPages', 'hasNext'],
                'filters' => ['states', 'tags', 'priceRange', 'familyIndexRange']
            ]);
    }
}
