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
				<?php $speciesArray = explode('|', (string)$species->label); ?>
				<tr>
					@if ($speciesNameType === 'scientific' || $speciesNameType === 'axiophyte')
						<td class="d-none d-md-table-cell">{{ $speciesArray[4] }}</td>
						<td><?=$speciesArray[0]?></td>
						<td class="d-none d-sm-table-cell">{{ $speciesArray[2] }}</td>
						<td><?=$species->count?></td>
						<td><a href="/site/{{ $gridSquare }} /species/{{ $speciesArray[0] }}">see records</a></td>
					@endif
					@if ($speciesNameType === 'common')
						<td class="d-none d-md-table-cell">{{ $speciesArray[5] }}</td>
						<td><?=$speciesArray[1]?></td>
						<td class="d-none d-sm-table-cell">{{ $speciesArray[3] }}</td>
						<td><?=$species->count?></td>
						<td><a href="/site/{{ $gridSquare }}/species/{{ $speciesArray[1] }}">see records</a></td>
					@endif
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
	const map = initialiseBasicMap()

			// Unless the first occurrence didn't contain a site location, create a
		// marker for the site's location
		@if (!empty($siteLocation))
		const siteMarker = L.marker(<?= json_encode($siteLocation) ?>, {
			opacity: 0.75
		});
		siteMarker.addTo(map);
		@endif
</script>
@else
<div class="alert alert-warning" role="alert">
	No records could be found matching those criteria.
</div>
@endif

@include('partials/pagination')

@include('partials/download-link')
