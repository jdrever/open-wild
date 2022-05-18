@if ($showResults)
    @if (isset($results->records)&&count($results->records)>0)

        @include('partials/download-link')

<table class="table mt-3">
    <thead>
        <tr>
            <th class="d-none d-md-table-cell">Family</th>
            <th <?php if ($speciesNameType === 'common') { ?>class="d-none d-sm-table-cell" <?php } ?>>Scientific Name</th>
            <th <?php if ($speciesNameType === 'scientific') { ?>class="d-none d-sm-table-cell" <?php } ?>>Common Name</th>
            <th>Records</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results->records as $species) { ?>
            <tr>
                <td class="d-none d-md-table-cell">{{ $species->family }}</td>
                <td><a href="{{ '/species/' . $species->scientificName . '?page=1' }}">{{ $species->scientificName }}</a></td>
                <td class="d-none d-sm-table-cell">
                    <a href="{{ '/species/' . $species->scientificName . '?page=1&speciesNameToDisplay=' . $species->commonName }}">{{ $species->commonName }}</a>
                </td>
                <td>{{ $species->recordCount }}</td>
            </tr>
        <?php } ?>
    </tbody>
</table>
@include('partials/download-link')

@include('partials/pagination')

    @else
        @include('partials/no-records')
    @endif
    @include('partials/nbn-query')
@endif

