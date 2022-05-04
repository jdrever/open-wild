<?php

namespace App\Http\Livewire;

use Livewire\Component;

class SpeciesAutocomplete extends Component
{

    public string $autoSpeciesName="";

    public function render()
    {
        return view('livewire.species-autocomplete');
    }
}
