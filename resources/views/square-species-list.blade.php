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

<?php if ($status ==='OK') : ?>

<div class="d-flex align-items-center">
	<a href="/squares" class="header-backArrow">
		<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16">
			<path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z" />
		</svg>
	</a>
	<h2>
		Species in Grid Square {{ $gridSquare }}
	</h2>
</div>

<form action="/" action="post">
    @csrf
    <div class="row justify-content-center gy-3">
        <div class="form-group col-sm-4 col-lg-3">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="name-type" id="scientific-name" value="scientific" onchange="this.form.submit();" <?= set_radio('name-type', 'scientific', ($nameType === 'scientific')); ?> />
                <label class="form-check-label" for="scientific-name">scientific<span class="d-none d-lg-inline"> name</span></label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="name-type" id="common-name" value="common"  onchange="this.form.submit();" <?= set_radio('name-type', 'common', ($nameType === 'common')); ?> />
                <label class="form-check-label" for="common-name">common<span class="d-none d-lg-inline"> name only</span></label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="axiophyte-filter" id="axiophyte-filter" value="true"  onchange="this.form.submit();" <?= set_radio('axiophyte-filter', 'true', ($axiophyteFilter === 'true')); ?> />
                <label class="form-check-label" for="axiophyte-name">
                    <span class="d-lg-none">axiophytes</span>
                    <span class="d-none d-lg-inline">axiophytes only</span>
                </label>
            </div>
        </div>
        <div class="form-group col-sm-4 col-lg-3">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="species-group" id="plants" value="plants"  onchange="this.form.submit();" <?= set_radio('groups', 'plants', $speciesGroup === 'plants'); ?> />
                <label class="form-check-label" for="plants">only plants</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="species-group" id="bryophytes" value="bryophytes"  onchange="this.form.submit();" <?= set_radio('groups', 'bryophytes', $speciesGroup === 'bryophytes'); ?> />
                <label class="form-check-label" for="bryophytes">only bryophytes</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="species-group" id="both" value="both"  onchange="this.form.submit();" <?= set_radio('groups', 'both', $speciesGroup === 'both'); ?> />
                <label class="form-check-label" for="both">both <span class="d-none d-xl-inline">plants and bryophytes</span></label>
            </div>
        </div>
    </div>
</form>


<?php if (isset($results->records)&&count($results->records)>0) { ?>
<!-- Display search results and map showing square location-->
<ul id="tabs" class="nav nav-tabs d-lg-none" role="tablist">
	<li class="nav-item" role="presentation">
		<button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#data" type="button" role="tab" aria-controls="data" aria-selected="true">Data</button>
	</li>
	<li class="nav-item" role="presentation">
		<button class="nav-link" id="home-tab" data-bs-toggle="tab" data-bs-target="#map-container" type="button" role="tab" aria-controls="map" aria-selected="false">Map</button>
	</li>
</ul>
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
				<?php foreach ($records as $species) : ?>
				<?php $speciesArray = explode('|', (string)$species->label); ?>
				<tr>
					<?php if ($speciesNameType === 'scientific' || $speciesNameType === 'axiophyte') : ?>
						<td class="d-none d-md-table-cell">{{ $speciesArray[4] }}</td>
						<td><?=$speciesArray[0]?></td>
						<td class="d-none d-sm-table-cell">{{ $speciesArray[2] }}</td>
						<td><?=$species->count?></td>
						<td><a href="/square/{{ $gridSquare }} /species/{{ $speciesArray[0] }}">see records</a></td>
					<?php endif?>
					<?php if ($speciesNameType === 'common') : ?>
						<td class="d-none d-md-table-cell">{{ $speciesArray[5]?></td>
						<td><?=$speciesArray[1]?></td>
						<td class="d-none d-sm-table-cell">{{ $speciesArray[3]?></td>
						<td><?=$species->count?></td>
						<td><a href="/square/{{ $gridSquare }}/species/{{ $speciesArray[1] }}">see records</a></td>
					<?php endif?>
				</tr>
				<?php endforeach ?>
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
<?php else: ?>
<div class="alert alert-warning" role="alert">
	No records could be found matching those criteria.
</div>
<?php endif ?>

<?= $this->include('pagination') ?>

<?php endif ?>

</x-layout>
