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
	<a href="/sites/" class="header-backArrow">
		<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16">
			<path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z" />
		</svg>
	</a>
	<h2>
		Species in {{ $siteName }}
	</h2>
</div>

<form action="/" action="post">
    @csrf
    @include('partials/search-selections')
</form>

<div id="data-table">
    @include("data-tables/species-in-site")
</div>

<script type="text/javascript" src="/js/update-dataset.js" defer></script>
<script>
function getUpdateUrl(pageNumber)
{
    let speciesNameType=document.querySelector('input[name="speciesNameType"]:checked').value;
    let speciesGroup=document.querySelector('input[name="speciesGroup"]:checked').value;
    let axiophyteFilter=document.getElementById("axiophyteFilter").checked;
    let updateUrl='/site/{{ $siteName }}/type/'+speciesNameType+'/group/'+speciesGroup+'/axiophytes/'+axiophyteFilter+'/refresh?page='+pageNumber;
    return updateUrl;
}
</script>

</x-layout>
