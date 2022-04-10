<x-layout>

<script>
function updateDataset(pageNumber)
{
showSpinner();
let speciesName=document.getElementById("speciesName").value;
let speciesNameType=document.querySelector('input[name="speciesNameType"]:checked').value;;
let updateUrl='/species-update/'+speciesName+'/type/'+speciesNameType+'/group/plants/axiophytes/false?page='+pageNumber;
console.log(updateUrl);
fetch(updateUrl).then(function (response) {
	// The API call was successful!
	return response.text();
}).then(function (html) {
    var elem = document.querySelector('#data-table');

    //Set HTML content
    elem.innerHTML = html;

}).catch(function (err) {
	// There was an error
	console.warn('Something went wrong.', err);
});
}

function showSpinner() {
    var elem = document.querySelector('#data-table');
    elem.innerHTML = '<div class="text-center"><button class="btn btn-primary" type="button" disabled><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading... </button></div>';
}
</script>

<h2 class="text-start text-md-center">Search for a Species in Shropshire</h2>

<form action="/" action="post">
@csrf
<div class="row mb-2">
	<div class="col-lg-8 mx-auto">
		<label for="search" class="form-label visually-hidden">Species name</label>
		<div class="input-group">
			<input type="text" id="speciesName" class="form-control" name="speciesName" aria-describedby="search-help" placeholder="Species name" value="{{ $speciesName }}" />
			<button type="submit" onclick="updateDataset(1); return false;" class="btn btn-primary">List Species</button>
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
			<input class="form-check-input" type="checkbox" name="axiophyte-filter" id="axiophyte-filter" value="true"  onchange="if (this.form.search.value!='') { this.form.submit(); }" {{ ($axiophyteFilter=="true")? "checked" : "" }} />
			<label class="form-check-label" for="axiophyte-name">
				<span class="d-lg-none">axiophytes</span>
				<span class="d-none d-lg-inline">axiophytes only</span>
			</label>
		</div>
	</div>
	<div class="form-group col-sm-4 col-lg-3">
		<div class="form-check">
			<input class="form-check-input" type="radio" name="speciesGroup" id="plants" value="plants"  onchange="if (this.form.search.value!='') { this.form.submit(); }" {{ ($speciesGroup=="plants")? "checked" : "" }} />
			<label class="form-check-label" for="plants">
				only plants
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="speciesGroup" id="bryophytes" value="bryophytes"  onchange="if (this.form.search.value!='') { this.form.submit(); }" {{ ($speciesGroup=="bryophytes")? "checked" : "" }} />
			<label class="form-check-label" for="bryophytes">
				only bryophytes
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="speciesGroup" id="both" value="both"  onchange="if (this.form.search.value!='') { this.form.submit(); }" {{ ($speciesGroup=="both")? "checked" : "" }} />
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
