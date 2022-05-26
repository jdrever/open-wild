<x-layout>

<div class="d-flex align-items-center">

		<a id="back-link" href="/species/{{ $results->scientificName }}?displayName={{ $displayName }}" class="header-backArrow">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16">
				<path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z" />
			</svg>
		</a>

	<h2>{{ $displayTitle }}</h2>
</div>
	<ul id="tabs" class="nav nav-tabs d-lg-none" role="tablist">
		<li class="nav-item" role="presentation">
			<button class="nav-link active fw-bold" id="map-tab" data-bs-toggle="tab" data-bs-target="#data" type="button" role="tab" aria-controls="data" aria-selected="true">Data</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link fw-bold" id="data-tab" data-bs-toggle="tab" data-bs-target="#map-container" type="button" role="tab" aria-controls="map" aria-selected="false">Map</button>
		</li>
	</ul>

	<div id="tab-content" class="row">
		<div id="data" class="tab-pane fade show active col-lg">
			<table class="table table-sm">
				<thead>
					<tr>
						<th scope="col">Record Id</th>
						<th scope="col">{{ $results->recordId }}</th>
					</tr>
				</thead>
				<tbody>
                    <tr>
                        <th scope="col" colspan="2">Overview</th>
                    </tr>
					<tr>
						<td scope="row">Site Name</td>
						<td scope="row">@isset ($results->SiteName) <a href="/site/{{ $results->siteName }}/species/{{ $results->scientificName }}">{{ $results->siteName }}</a> @endisset</td>
					</tr>
                    <tr>
						<td scope="row">Locality</td>
						<td scope="row">{{ $results->locality }}</a></td>
					</tr>
					<tr>
						<td scope="row">Full Grid Reference</a></td>
						<td scope="row"><a href="/square/{{ $results->gridReference }}/species/{{ $results->scientificName }}">{{ $results->gridReference }}</a></td>
					</tr>
                    <tr>
						<td scope="row">Scientific Name</td>
						<td scope="row">{{ $results->scientificName }}</td>
					</tr>
					<tr>
						<td scope="row">Common Name</td>
						<td scope="row">
							{{ $results->commonName }}
						</td>
					</tr>
					<tr>
						<td scope="row">Recorders</td>
						<td scope="row">{{ $results->recorders }}</td>
					</tr>
					<tr>
						<td scope="row">Full Date</td>
						<td scope="row">{{ $results->fullDate }}</td>
					</tr>
                    <tr>
                        <th scope="col" colspan="2">Taxonomy
                        </th>
                    </tr>
					<tr>
						<td scope="row">Species</td>
						<td scope="row">{{ $results->species }}</td>
					</tr>
					<tr>
						<td scope="row">Genus</td>
						<td scope="row">{{ $results->genus }}</td>
					</tr>
					<tr>
						<td scope="row">Family</td>
						<td scope="row">{{ $results->family }}</td>
					</tr>
					<tr>
						<td scope="row">Order</td>
						<td scope="row">{{ $results->order }}</td>
					</tr>
					<tr>
						<td scope="row">Class</td>
						<td scope="row">{{ $results->class }}</td>
					</tr>
					<tr>
						<td scope="row">Phylum</td>
						<td scope="row">{{ $results->phylum }}</td>
					</tr>
					<tr>
						<td scope="row">Kingdom</td>
						<td scope="row">{{ $results->kingdom }}</td>
					</tr>
                    <tr>
                        <th scope="col" colspan="2">Observation
                        </th>
                    </tr>
                    <tr>
						<td scope="row">Basis of Record</td>
						<td scope="row">
							{{ $results->basisOfRecord }}
						</td>
					</tr>
                    <tr>
						<td scope="row">Verification Status</td>
						<td scope="row">
							{{ $results->verificationStatus }}
						</td>
					</tr>
                    <tr>
						<td scope="row">Remarks</td>
						<td scope="row">
							{{ $results->remarks }}
						</td>
					</tr>
                    <tr>
                        <th scope="col" colspan="2">Attribution
                        </th>
                    </tr>
                    <tr>
						<td scope="row">Data Provider</td>
						<td scope="row">
							{{ $results->dataProvider }}
						</td>
					</tr>
                    <tr>
						<td scope="row">License</td>
						<td scope="row">
							{{ $results->license }}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="map-container" class="tab-pane fade show col-lg">
			<div id="map" class=""></div>
		</div>
	</div>
	@isset($results->gridReferenceWKT)
		<script>
			// Basic map code (in BasicMap.js) - not fitting to Shropshire
			const map = initialiseBasicMap(fitToShropshire = false);

			// Initialise geoJson boundary layer for wkt polygon
			const wktBoundary = L.geoJSON(null, {
				"color": "#0996DB",
				"weight": 5,
				"opacity": 0.33
			});

			// Create outline of record location and zoom to fit to it
			const wkt = new Wkt.Wkt();
			wkt.read("{{ $results->gridReferenceWKT }}");
			const wktRecord = wkt.toJson();
			wktBoundary.addData(wktRecord);
			wktBoundary.addTo(map)
			map.fitBounds(wktBoundary.getBounds(wktRecord).pad(0.5));
		</script>
	@endisset

    @include('partials/download-link')

    @include('partials/nbn-query')



</x-layout>
