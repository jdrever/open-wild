@isset($results->records)
@foreach ($results->records as $record)
    <option value="{{$record}}">
@endforeach
@endisset
