<?php

namespace Tests\Feature;

use Tests\TestCase;

class IntegrationTests extends TestCase
{
    /**
     * Does the root application return a success response?
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Does the species search return a success response?
     *
     * @return void
     */
    public function test_species_search_returns_a_successful_response()
    {
        $response = $this->get('/species/Hedera/type/scientific/group/plants/axiophytes/false');

        $response->assertStatus(200);
    }
}
