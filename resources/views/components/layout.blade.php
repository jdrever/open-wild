<!doctype html>
<html lang="en">

<head>

	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta content="Promoting the enjoyment, understanding and conservation of the flora of Shropshire" name="description" />
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
	<!-- Custom styles for this template -->
	<link href="/css/NOapp.css" rel="stylesheet">
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
	</script>
	<title>
    {{ $title ?? 'Shrop Bot' }}
	</title>
</head>

<body>
<header>
  <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Botanical Records</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav me-auto mb-2 mb-md-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">Species</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Sites</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Squares</a>
          </li>
          <li class="nav-item">
		    <a class="nav-link" href="/about">About</a>
		</li>
        </ul>
    </div>
  </nav>
</header>

<?php if (isset($error)) : ?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <?= $error ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif ?>
<div class="container-fluid content-inner p-3">
{{ $slot }}
</div>
<footer class="page-footer footer-fluid">
    <div class="mx-auto mt-2 text-center">
        <span class="small">Shropshire Botanical Society Data supported by
            <a href="https://registry.nbnatlas.org/public/show/dp120" target="_blank">National Biodiversity Network</a>
        </span>
<?php if (isset($queryUrl)) { ?>
        <details style="font-size:small;"><summary>NBN API Query</summary><?= $queryUrl ?></details>
<?php } ?>
    </div>
</footer>
<!-- Bootstrap 5-beta2 bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
</body>
</html>
