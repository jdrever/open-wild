<x-layout>

<style>
    #map {
      max-width: 960px;
      height: 500px;
    }
    svg {
      position: relative;
    }
    .square {
      fill: #000;
      fill-opacity: .2;
      stroke-width: 1px;
    }
    .c0 {
      stroke: #fff;
    }
    .c1 {
      stroke: red;
    }
    .c2 {
      stroke: blue;
    }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol/dist/L.Control.Locate.min.css" />
<script src="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol/dist/L.Control.Locate.min.js" charset="utf-8"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.18.3/highlight.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.18.3/styles/a11y-light.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.18.3/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>

<script src="https://unpkg.com/brc-atlas-bigr/dist/bigr.min.umd.js"></script>
<script src="https://d3js.org/d3.v5.min.js"></script>

<h2 class="text-start text-md-center">Search for a Square in {{ config('core.region') }}</h2>
<div class="row mb-2">
    <div class="col-lg-8 mx-auto">
<p id="selection"> Zoom in to select a 1km square </p>

<div id="map"></div>
<script>
	// Get mapState from the php data and initialise the map using that state
	// (and not fitting to shropshire or handling resize)
	var mapState = {!! json_encode(explode(",", $mapState)) !!};
	const map = initialiseBasicMap('{{ config('core.region') }}',fitToRegion = false, handleResize = false, mapState = mapState)

	L.control.locate().addTo(map)

	var svg = d3.select(map.getPanes().overlayPane).append("svg")
    var g = svg.append("g").attr("class", "leaflet-zoom-hide")
    var transform = d3.geoTransform({point: projectPoint})
    var path = d3.geoPath().projection(transform)
    var ftrSquares, squares, grs

	map.on('click', function(e) {
		// Only let the user click on a square if the map is zoomed in enough
		// (such that the 1km grid graticule is shown)
		// TODO - check if e.latlng is within Shropshire boundary?
		if (map.getZoom() >= 11) {
			// Go to species list for square page
			grs = bigr.getGrFromCoords(e.latlng.lng, e.latlng.lat, 'wg', '', [100000, 10000, 5000, 1000])
			window.location.href = "/square/" + grs.p1000 + "/type/{{ $speciesNameType }}/group/{{ $speciesGroup }}/axiophytes/{{ $axiophyteFilter }}?mapState="+mapState;
		}
	})
    map.on("zoomend", reset)
    map.on("mousemove", function(e) {
		grs = bigr.getGrFromCoords(e.latlng.lng, e.latlng.lat, 'wg', '', [100000, 10000, 5000, 1000])

		if (!grs.p100000) return

      	var ftr1 = {
        	type: 'Feature',
        	geometry: bigr.getGjson(grs.p100000, 'wg', 'square')
      	}
      	var ftr2 = {
        	type: 'Feature',
        	geometry: bigr.getGjson(grs.p10000, 'wg', 'square')
      	}
      	var ftr3 = {
        	type: 'Feature',
        	geometry: bigr.getGjson(grs.p5000, 'wg', 'square')
      	}
      	var ftr4 = {
        	type: 'Feature',
        	geometry: bigr.getGjson(grs.p1000, 'wg', 'square')
      	}

		ftrSquares = [ftr1, ftr2, ftr3, ftr4]

		squares = g.selectAll("path").data(ftrSquares)

		squares.enter()
			.append("path")
			.attr("d", path)
			.attr("class", function(d, i) {
				// Add ci and square classes to style the path of the
				// highlighted square
				return 'square c' + i
			})

		reset()
	});

	function reset() {
		var bounds = path.bounds({
			type: "FeatureCollection",
			features: ftrSquares
		})

		var topLeft = bounds[0]
		var bottomRight = bounds[1]

		svg.attr("width", bottomRight[0] - topLeft[0])
			.attr("height", bottomRight[1] - topLeft[1])
			.style("left", topLeft[0] + "px")
			.style("top", topLeft[1] + "px")

		g.attr("transform", "translate(" + -topLeft[0] + "," + -topLeft[1] + ")")
		squares.attr("d", path)

		if (!grs) return

		// Update currently selected square text and hide the 1km square (the
		// fourth of the grs feature squares, class "c3")
		if (map.getZoom() >= 11) {
			document.getElementById("selection").innerHTML = "Currently selecting: <b>" + grs.p1000 + "</b>"
			g.select(".c3").style("fill-opacity", 0.2)
		}
		else {
			document.getElementById("selection").innerHTML = "Zoom in to select a 1km square"
			g.select(".c3").style("fill-opacity", 0)
		}

        mapState=map.getCenter().lat+","+map.getCenter().lng+","+map.getZoom();
		// Update mapState cookie with map position and zoom
		//document.cookie = "mapState="+map.getCenter().lat+","+map.getCenter().lng+","+map.getZoom()+";SameSite=Lax;";
	}

    function projectPoint(x, y) {
		var point = map.latLngToLayerPoint(new L.LatLng(y, x))
    	this.stream.point(point.x, point.y)
    }
</script>

    </div>
</div>

</x-layout>
