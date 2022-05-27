<!doctype html>
<html lang="en">

<head>

	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta content="Promoting the enjoyment, understanding and conservation of the flora of Shropshire" name="description" />
	<!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/' . strtolower(config('core.region')) .'-style.css') }}">
	<!-- Custom styles for this template -->
	<link href="/css/enhancements.css" rel="stylesheet">
	<!-- A2HS -->
	<link rel="manifest" href="/manifest.webmanifest">
	<!-- Mapping -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.css" integrity="sha256-BPfK9M5v34c2XP6p0cxVz1mUQLst0gTLk0mlc7kuodA=" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet-src.js" integrity="sha256-dG7rUAec0UN0e3nlMEuYJzsbK0rpoz4hBbNp0ir9iF8=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/wicket@1.3.6/wicket.min.js"></script>
	<script type="text/javascript" src="/js/proj4.js"></script>
	<script type="text/javascript" src="/js/Leaflet.MetricGrid.js"></script>
	<script type="text/javascript" src="/js/leaflet.wms.js"></script>
	<script type="text/javascript" src="/js/BasicMap.js"></script>
	<script>
		if (navigator && navigator.serviceWorker)
		{
  			navigator.serviceWorker.register('/js/sw.js');
		}

        var element = document.getElementById('back-link');

        if (element)
        {
            // Provide a standard href to facilitate standard browser features such as
            //  - Hover to see link
            //  - Right click and copy link
            //  - Right click and open in new tab
            element.setAttribute('href', document.referrer);

            // We can't let the browser use the above href for navigation. If it does,
            // the browser will think that it is a regular link, and place the current
            // page on the browser history, so that if the user clicks "back" again,
            // it'll actually return to this page. We need to perform a native back to
            // integrate properly into the browser's history behavior
            element.onclick = function() {
            history.back();
            return false;
        }
}
	</script>

	<title>
    {{ $title ?? 'WildSearch' }}
	</title>
</head>

<body>
@include('components/header')

@isset($results->errorMessage)
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <?= $results->errorMessage ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endisset

<div class="container-fluid content-inner p-3">
{{ $slot }}
</div>
<footer class="page-footer footer-fluid">
    <div class="mx-auto mt-2 text-center">
        <span class="small">Supported by
            <a href="https://registry.nbnatlas.org/public/show/dp120" target="_blank">National Biodiversity Network</a>
        </span>
    </div>
</footer>
<!-- Bootstrap 5-beta2 bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
</body>
</html>
