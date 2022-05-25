<header>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="/">{{ env('SITE_NAME','WildSearch') }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <ul class="navbar-nav me-auto mb-2 mb-md-0">
@if (env('SPECIES_SEARCH'))
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="/">Species</a>
            </li>
@endif
@if (env('SITES_SEARCH'))
            <li class="nav-item">
              <a class="nav-link" href="/sites/">Sites</a>
            </li>
@endif
@if (env('SQUARES_SEARCH'))
            <li class="nav-item">
              <a class="nav-link" href="/squares/">Squares</a>
            </li>
@endif
            <li class="nav-item">
              <a class="nav-link" href="/about">About</a>
          </li>
          </ul>
      </div>
    </nav>
  </header>
