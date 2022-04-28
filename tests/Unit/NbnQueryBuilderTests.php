<?php declare(strict_types = 1);

namespace Tests\Unit;


use PHPUnit\Framework\TestCase;
use App\Services\NbnQueryBuilder;

class NbnQueryBuilderTests extends TestCase
{
    /**
     * tests for uuid 12345
     *
     * @return void
     */
    public function test_occurrence_query_is_correct()
    {
        $uuid="12345";
        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURRENCE);
        $this->assertEquals("https://records-ws.nbnatlas.org/occurrence/" . $uuid,$nbnQuery->url() . '/'.  $uuid);
    }

    /**
     * Tests for common name search for ivy, both plants and bryophytes
     *
     * @return void
     */
    public function test_occurrence_search_ivy_common_both_correct()
    {

        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURRENCES_SEARCH);
        $nbnQuery->addSpeciesNameType("common", "*Ivy*");
        $nbnQuery->addSpeciesGroup("BOTH");
        $this->assertEquals("https://records-ws.nbnatlas.org/occurrences/search?q=data_resource_uid:dr782&fq=common_name:*Ivy*%20AND%20species_group:Plants+OR+Bryophytes&facets=common_name_and_lsid&sort=&fsort=index&dir=asc&flimit=10&facet.offset=0",$nbnQuery->getPagingQueryString());
    }

    /**
     * Tests for scientfic name search for hedera, both plants and bryophytes
     *
     * @return void
     */
    public function test_occurrence_search_hedera_scientific_both_correct()
    {

        $nbnQuery = new NbnQueryBuilder(NbnQueryBuilder::OCCURRENCES_SEARCH);
        $nbnQuery->addSpeciesNameType("scientific", "*Hedera*");
        $nbnQuery->addSpeciesGroup("BOTH");
        $this->assertEquals("https://records-ws.nbnatlas.org/occurrences/search?q=data_resource_uid:dr782&fq=taxon_name:*Hedera*%20AND%20species_group:Plants+OR+Bryophytes&facets=names_and_lsid&sort=&fsort=index&dir=asc&flimit=10&facet.offset=0",$nbnQuery->getPagingQueryString());
    }
}
