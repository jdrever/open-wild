function initialiseBasicMap(region, fitToRegion = true, handleResize = true, mapState =  [52.6354, -2.71975, 9]) {

	// Initialise the map
	// mapState = map center lat, map center lng, map zoom level
	const map = L.map("map", {
		zoomSnap: 0,
	}).setView(mapState.slice(0, 2), mapState[2]);

	// Make a minimal base layer using Mapbox data
	const minimal = L.tileLayer("https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}", {
		attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
		maxZoom: 18,
		id: "mapbox/outdoors-v11",
		tileSize: 512,
		zoomOffset: -1,
		accessToken: "pk.eyJ1IjoiY2hhcmxlc3JvcGVyIiwiYSI6ImNrbmY2YXl4ZTJjbDQydm1xOW83MXh1eDIifQ.ntclZm-a8OxwUEBODW08FQ"
	});

	// Initialise geoJson boundary layer
	const boundary = L.geoJSON(null, {
		"color": "#0996DB",
		"weight": 5,
		"opacity": 0.33
	});

	// OS Grid graticules
	// 10km grid graticule shown between zoom levels 8 and 11 and has no axis labels
	const graticule10km = L.britishGrid({
		color: '#216fff',
		weight: 1,
		showAxisLabels: [],
		minInterval: 10000,
		maxInterval: 10000,
		minZoom: 8,
		maxZoom: 11
	});

	// 1km grid graticule shown between zoom levels 11 and 15 and has labelled axis
	const graticule1km = L.britishGrid({
		color: '#216fff',
		weight: 1,
		showAxisLabels: [1000],
		minInterval: 1000,
		maxInterval: 1000,
		minZoom: 11,
		maxZoom: 15
	});

	// Create a Layer Group and add to map
	const layers = L.layerGroup([minimal, boundary, graticule10km, graticule1km]);
	layers.addTo(map);

	// We load the geojson data from disk using the JavaScript Fetch API. When
	// the response resolves, we add the data to the boundary layer and use the
	// fitBounds() Leaflet method to zoom and position the map around the
	// boundary data with a touch of padding.
	const url = "/data/"+region+"_simple.json";
	fetch(url)
		.then((response) => response.json())
		.then((geojson) => {
			boundary.addData(geojson);

			// Fit map to shropshire bounds - true by default
			if (fitToRegion) {
				map.fitBounds(boundary.getBounds(geojson).pad(0.1));
			}
		});

	if (handleResize) {
		["load", "resize"].forEach((event) => {
			window.addEventListener(event, () => {
				const activeTab = document.querySelector("[aria-selected='true']");
				new bootstrap.Tab(activeTab).show();

				if (window.matchMedia("(min-width: 992px)").matches) {
					document.querySelector("#tab-content").classList.remove("tab-content");
					document.querySelector("#map-container").classList.add("show");
					document.querySelector("#data").classList.add("show");
				} else {
					document.querySelector("#tab-content").classList.add("tab-content");
					bootstrap.Tab.getInstance(activeTab).show();
				}
			});
		});
	}

	return map;
}
