<input type="text" id="speciesName" class="form-control" name="speciesName" aria-describedby="search-help" placeholder="Species name" value="{{ $speciesName }}" wire:model.debounce.50ms="autoSpeciesName" list="auto" />
{{ $queryUrl }}
@isset($records)
<datalist id="auto">
@foreach ($records as $record)
    <option value="{{$record}}">
@endforeach
</datalist>
@endisset
