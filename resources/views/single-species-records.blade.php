<x-layout>

<div class="d-flex align-items-center">
	<a id="back-link" href="/species/{{ $speciesNameSearchedFor }}/type/{{ $speciesNameType }}/group/{{ $speciesGroup }}/axiophytes/{{ $axiophyteFilter }}" class="header-backArrow">
		<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16">
			<path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z" />
		</svg>
	</a>
	<h2>
		{{ urldecode($speciesNameToDisplay) }} records in {{ config('core.region') }}
	</h2>
</div>

@if (isset($results->records)&&count($results->records)>0)

    @include('partials/download-link')

    @include('partials/data-map-tabs')

<div id="data-table">
<div id="tab-content" class="row">
	<div id="data" class="tab-pane fade show active col-lg">
        <table class="table">
            <thead>
                <tr>
                    <th>Site</th>
                    <th class="d-none d-sm-table-cell">Square</th>
                    <th class="d-none d-md-table-cell">Collector</th>
                    <th>Year</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($results->records as $record)
                    <tr data-uuid="{{ $record->recordId }}">
                        <td>
                            <a href="/site/{{ $record->site }}/species/{{$speciesName }}">
                                <?= $record->site ?>
                            </a>
                        </td>
                        <td class="d-none d-sm-table-cell">
                        <?php if (isset($record->square)) : ?>
                            <a href="/square/{{ $record->square }}/species/{{$speciesName}}">
                                {{$record->square}}
                            </a>
                        <?php endif ?>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <?= $record->collector ?>
                        </td>
                        <td>
                            <?= $record->year ?>
                        </td>
                        <td>
                            <a href="/record/{{ $record->recordId}}">
                                more
                            </a>
                        </td>
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
	const map = initialiseBasicMap('{{ config('core.region')}}');

	// Make a dot map layer
	const wmsUrl = "https://records-ws.nbnatlas.org/mapping/wms/reflect?" +
		"Q=lsid:{{ $results->records[0]->speciesGuid }}" +
		"&ENV=colourmode:osgrid;color:ffff00;name:circle;size:4;opacity:0.5;" +
		"gridlabels:true;gridres:singlegrid" +
		"&fq=data_resource_uid:{{ config('core.dataResourceId') }}";

	const species = L.tileLayer.wms(wmsUrl, {
		"layers": "ALA:occurrences",
		"uppercase": true,
		"format": "image/png",
		"transparent": true
	});

	species.addTo(map);

	// Plot page of records on the map with tooltips
	const records = {!! json_encode($results->records) !!};

	const recordMarkers = records.map(record => {
		const lat = record.decimalLatitude;
		const lng = record.decimalLongitude;

		const marker = L.circleMarker([lat, lng], {
			fillColor: "red",
			color: "darkRed",
			fillOpacity: .75
		});

		marker.uuid = record.recordId;

		marker.bindPopup(`
			${record.site} (${record.square})<br>
			${record.collector}<br>
		`);

		// When we open a popup also highlight the corresponding row in the data
		// table. This is _essential_ for orientation when using tabs on small
		// screens
		marker.on("popupopen", (event) => {
			const highlightRow = document.querySelector(`[data-uuid="${event.target.uuid}"]`);
			highlightRow.style.backgroundColor = 'rgb(255, 255, 0, 0.5)';
		});

		marker.on("popupclose", (event) => {
			const highlightRow = document.querySelector(`[data-uuid="${event.target.uuid}"]`);
			highlightRow.style.backgroundColor = 'initial';
		})

		return marker;
	});
	L.layerGroup(recordMarkers).addTo(map);

	// Plot the sites to the map as markers
	const sites = {!! json_encode($results->sites) !!};
	const siteMarkers = Object.entries(sites).map(site => {
		return L.circleMarker(site[1]).bindTooltip(site[0]);
	});
	// Turn off rendering of sites for now
	// L.layerGroup([...siteMarkers]).addTo(map);
</script>

    @include('partials/pagination')

    @include('partials/download-link')


@else
    @include('partials/no-records')
@endif

@include('partials/nbn-query')
</div>


</x-layout>
