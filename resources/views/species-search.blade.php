
<x-layout>

<script type="text/javascript" src="/js/update-dataset.js"></script>
<script>
function getUpdateUrl(pageNumber)
{
    let speciesName=document.getElementById("speciesName").value;
    let speciesNameType=document.querySelector('input[name="speciesNameType"]:checked').value;
    let speciesGroup=document.querySelector('input[name="speciesGroup"]:checked').value;
    let axiophyteFilter=document.getElementById("axiophyteFilter").checked;
    let updateUrl='/species-update/'+speciesName+'/type/'+speciesNameType+'/group/'+speciesGroup+'/axiophytes/'+axiophyteFilter+'?page='+pageNumber;
    return updateUrl;
}
</script>


<h2 class="text-start text-md-center">Search for a Species in {{ env('AREA') }}</h2>

<form action="/" action="post">
@csrf
<div class="row mb-2">
	<div class="col-lg-8 mx-auto">
		<label for="search" class="form-label visually-hidden">Species name</label>
		<div class="input-group">
			<input type="text" id="speciesName" class="form-control" name="speciesName" aria-describedby="search-help" placeholder="Species name" value="{{ $speciesName }}" />
			<button type="submit" onclick="return updateDataset(1);" class="btn btn-primary">List Species</button>
		</div>
		<small id="search-help" class="form-text text-start text-md-center d-block">Enter all or part of a species name. Try something like "Hedera".</small>
	</div>
</div>
<div class="row justify-content-center gy-3">
	<div class="form-group col-sm-4 col-lg-3">
		<div class="form-check">
			<input class="form-check-input" type="radio" name="speciesNameType" id="speciesNameType" value="scientific" onchange="updateDataset(1);" {{ ($speciesNameType=="scientific")? "checked" : "" }} />
			<label class="form-check-label" for="scientific-name">
				scientific<span class="d-none d-lg-inline"> name only</span>
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="speciesNameType" id="speciesNameType" value="common"  onchange="updateDataset(1);" {{ ($speciesNameType=="common")? "checked" : "" }} />
			<label class="form-check-label" for="common-name">
				common<span class="d-none d-lg-inline"> name only</span>
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="checkbox" name="axiophyteFilter" id="axiophyteFilter" value="true"  onchange="updateDataset(1);" {{ ($axiophyteFilter=="true")? "checked" : "" }} />
			<label class="form-check-label" for="axiophyte-name">
				<span class="d-lg-none">axiophytes</span>
				<span class="d-none d-lg-inline">axiophytes only</span>
			</label>
		</div>
	</div>
	<div class="form-group col-sm-4 col-lg-3">
		<div class="form-check">
			<input class="form-check-input" type="radio" name="speciesGroup" id="speciesGroup" value="plants" onchange="updateDataset(1);" {{ ($speciesGroup=="plants")? "checked" : "" }} />
			<label class="form-check-label" for="plants">
				only plants
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="speciesGroup" id="speciesGroup" value="bryophytes"  onchange="updateDataset(1);" {{ ($speciesGroup=="bryophytes")? "checked" : "" }} />
			<label class="form-check-label" for="bryophytes">
				only bryophytes
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="speciesGroup" id="speciesGroup" value="both"  onchange="updateDataset(1);" {{ ($speciesGroup=="both")? "checked" : "" }} />
			<label class="form-check-label" for="both">
				both <span class="d-none d-xl-inline">plants and bryophytes</span>
			</label>
		</div>
	</div>
</div>
</form>

<div id="data-table">
    @include('data-tables/species-in-dataset')
</div>

</x-layout>
