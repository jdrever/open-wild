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
                    <td><a href="/square/{{ $gridSquare }}/species/{{ $species->scientificName }}">see records</a></td>
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

	// Use d3 and bigr to convert the gridSquare into a path that is rendered
	// onto the map whenever it is zoomed to highlight the grid square
	var svg = d3.select(map.getPanes().overlayPane).append("svg")
    var g = svg.append("g").attr("class", "leaflet-zoom-hide");
    var transform = d3.geoTransform({point: projectPoint})
    var path = d3.geoPath().projection(transform)

    map.on("zoomend", reset)

    function reset() {
		var ftrSquare = {
      		type: 'Feature',
      		geometry: bigr.getGjson("{{ $gridSquare }}", 'wg', 'square')
    	}

    	var square = g.selectAll("path")
      		.data([ftrSquare])

    	square.enter()
      		.append("path")
      		.attr("d", path)
      		.attr("class", 'square')

      	var bounds = path.bounds({
        	type: "FeatureCollection",
        	features: [ftrSquare]
      	})

      	var topLeft = bounds[0]
      	var bottomRight = bounds[1]

      	svg.attr("width", bottomRight[0] - topLeft[0])
        	.attr("height", bottomRight[1] - topLeft[1])
        	.style("left", topLeft[0] + "px")
        	.style("top", topLeft[1] + "px")

      	g.attr("transform", "translate(" + -topLeft[0] + "," + -topLeft[1] + ")")

      	square.attr("d", path)
    }

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


