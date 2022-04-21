<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrationTests extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_species_search_returns_a_successful_response()
    {
        $response = $this->get('/species/Hedera/type/scientific/group/plants/axiophytes/false');

        $response->assertStatus(200);
    }
}
