<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\NbnQueryService;
use Tests\TestCase;

class NbnQueryServiceTests extends TestCase
{
    /**
     * tests for Hedera/scientific/plants+bryophytes/no axiophyte filter.
     *
     * @return void
     */
    public function test_species_list_for_dataset()
    {
        $speciesName = 'Hedera';
        $speciesNameType = 'Scientific';
        $speciesGroup = 'Both';
        $axiophyteFilter = 'false';
        $nbnQueryService = new NbnQueryService();
        $nbnQuery = $nbnQueryService->getSpeciesListForDataset($speciesName, $speciesNameType, $speciesGroup, $axiophyteFilter);
        $this->assertEquals(true, $nbnQuery->status);
    }
}
