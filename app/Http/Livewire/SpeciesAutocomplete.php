<?php

namespace App\Http\Livewire;

use App\Services\NbnQueryService;
use Livewire\Component;

class SpeciesAutocomplete extends Component
{
    public string $queryUrl = '';
    public string $autoSpeciesName = '';
    public string $speciesName = '';
    public string $message = '';
    public array $records;

    public function render()
    {
        if (! empty($this->autoSpeciesName)) {
            $nbnQuery = new NbnQueryService();
            $queryResult = $nbnQuery->getSpeciesNameAutocomplete($this->autoSpeciesName);
            $this->queryUrl = $queryResult->queryUrl;
            $this->message = $queryResult->message;

            $this->records = [];

            foreach ($queryResult->records as $record) {
                $this->records[] = $record->nameComplete;
            }
        }

        return view('livewire.species-autocomplete');
    }
}
