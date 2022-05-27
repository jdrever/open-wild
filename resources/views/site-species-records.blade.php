<x-layout>

<style>
	svg {
      position: relative;
    }
    .square {
      fill: #000;
      fill-opacity: .2;
	  stroke: red;
      stroke-width: 3px;
	}
</style>

<script src="https://unpkg.com/brc-atlas-bigr/dist/bigr.min.umd.js"></script>
<script src="https://d3js.org/d3.v5.min.js"></script>


<div class="d-flex align-items-center">
	<a href="javascript:history.back()" class="header-backArrow">
		<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16">
			<path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z" />
		</svg>
	</a>
	<h2>
		{{ $speciesName }} records in {{ $siteName }}
	</h2>
</div>


@if (isset($results->records)&&count($results->records)>0)

    @include('partials/download-link')

    @include('partials/data-map-tabs')

<div id="tab-content" class="row">
	<div id="data" class="tab-pane fade show active col-lg">
        <table class="table">
            <thead>
                <tr>
                    <th class="d-none d-sm-table-cell">Square</th>
                    <th class="d-none d-md-table-cell">Collector</th>
                    <th>Year</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($results->records as $record)
                    <tr data-uuid="{{ $record->recordId }}">
                        <td class="d-none d-sm-table-cell">
                            <a href="/square/{{ $record->square }}/species/{{ $speciesName }}">
                                <?= $record->square ?>
                            </a>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <?= $record->collector ?>
                        </td>
                        <td>
                            <?= $record->year ?>
                        </td>
                        <td>
                            <a href="/record/{{ $record->recordId }}")>
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
	const map = initialiseBasicMap('{{ config('core.region') }}')

		// Unless the first occurrence didn't contain a site location, create a
	// marker for the site's location
	@if (reset($results->sites))
		const siteMarker = L.marker({!! json_encode(reset($results->sites)) !!} , {
			opacity: 0.75
		});
		siteMarker.addTo(map)
	@endif

    function projectPoint(x, y) {
    	var point = map.latLngToLayerPoint(new L.LatLng(y, x))
		this.stream.point(point.x, point.y)
    }
</script>

    @include('partials/pagination')

    @include('partials/download-link')

@else
    @include('partials/no-records')
@endif

@include('partials/nbn-query')

</x-layout>
