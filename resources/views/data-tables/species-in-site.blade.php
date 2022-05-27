@if (isset($results->records)&&count($results->records)>0)

@include('partials/download-link')

@include('partials/data-map-tabs')

<div id="tab-content" class="row">
	<div id="data" class="tab-pane fade show active col-lg">
		<table class="table">
			<thead><tr>
				<th class="d-none d-md-table-cell">Family</th>
				<th>Scientific Name</th>
				<th class="d-none d-sm-table-cell">Common Name</th>
				<th>Count</th>
				<th>Records</th>
			</tr></thead>
			<tbody>
				@foreach ($results->records as $species)
				<tr>
						<td class="d-none d-md-table-cell">{{ $species->family }}</td>
						<td>{{ $species->scientificName }}</td>
						<td class="d-none d-sm-table-cell">{{ $species->commonName }}</td>
						<td><?=$species->recordCount?></td>
						<td><a href="/site/{{ $siteName }}/species/{{ $species->scientificName }}">see records</a></td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	<div id="map-container" class="tab-pane fade show col-lg">
		<div id="map" class=""></div>
	</div>
</div>
<script>
	// Initialise the map
	const map = initialiseBasicMap('{{ config('core.region') }}')
    addMarker();

    function addMarker()
    {
			// Unless the first occurrence didn't contain a site location, create a
		// marker for the site's location
		@if (!empty($results->siteLocation))
		const siteMarker = L.marker([{{ rtrim(implode(',',$results->siteLocation),',') }}], {
			opacity: 0.75
		});
		siteMarker.addTo(map);
		@endif
    }
</script>

@include('partials/pagination')

@include('partials/download-link')

@else
    @include('partials/no-records')
@endif

@include('partials/nbn-query')
