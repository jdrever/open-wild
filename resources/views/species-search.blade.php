<x-layout>

<h2 class="text-start text-md-center">Search for a Species in Shropshire</h2>

<form action="/" action="post">
@csrf
<div class="row mb-2">
	<div class="col-lg-8 mx-auto">
		<label for="search" class="form-label visually-hidden">Species name</label>
		<div class="input-group">
			<input type="text" id="speciesName" class="form-control" name="speciesName" aria-describedby="search-help" placeholder="Species name" value="{{ $speciesName }}" />
			<button type="submit" class="btn btn-primary">List Species</button>
		</div>
		<small id="search-help" class="form-text text-start text-md-center d-block">Enter all or part of a species name. Try something like "Hedera".</small>
	</div>
</div>
<div class="row justify-content-center gy-3">
	<div class="form-group col-sm-4 col-lg-3">
		<div class="form-check">
			<input class="form-check-input" type="radio" name="speciesNameType" id="scientific-name" value="scientific" onchange="if (this.form.search.value!='') { this.form.submit(); }" {{ ($speciesNameType=="scientific")? "checked" : "" }} />
			<label class="form-check-label" for="scientific-name">
				scientific<span class="d-none d-lg-inline"> name only</span>
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="speciesNameType" id="common-name" value="common"  onchange="if (this.form.search.value!='') { this.form.submit(); }" {{ ($speciesNameType=="common")? "checked" : "" }} />
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


{{-- //form_close() --}}
</form>

<!-- Show the search results if there are any -->
<?php if (isset($results->records)&&count($results->records)>0) { ?>

    @include('partials/download-link')


	<table class="table mt-3">
		<thead>
			<tr>
				<th class="d-none d-md-table-cell">Family</th>
				<th <?php if ($speciesNameType === 'common') { ?>class="d-none d-sm-table-cell" <?php } ?>>Scientific Name</th>
				<th <?php if ($speciesNameType === 'scientific') { ?>class="d-none d-sm-table-cell" <?php } ?>>Common Name</th>
				<th>Records</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($results->records as $species) { ?>
				<?php $speciesArray = explode('|', (string)$species->label); ?>
				<tr>
				<?php if ($speciesNameType === 'scientific') { ?>
					<td class="d-none d-md-table-cell"><?= $speciesArray[4] ?></td>
					<td><a href="{{ '/species/' . $speciesArray[0] . '?page=1' }}">{{ $speciesArray[0] }}</a></td>
					<td class="d-none d-sm-table-cell">
						<a href="{{ '/species/' . $speciesArray[0] . '?page=1&speciesNameToDisplay=' . $speciesArray[2] }}">{{ $speciesArray[2] }}</a>
					</td>
				<?php } ?>
				<?php if ($speciesNameType === 'common') { ?>
					<td class="d-none d-md-table-cell"><?= $speciesArray[5] ?></td>
                    <td class="d-none d-sm-table-cell"><a href="{{ '/species/' . $speciesArray[1] . '?page=1' }}">{{ $speciesArray[1] }}</a></td>
                    <td>
						<a href="{{ urldecode('/species/' . $speciesArray[1] . '?page=1&speciesNameToDisplay=' . $speciesArray[0]) }}">{{ $speciesArray[0] }}</a>
					</td>
				<?php } ?>
					<td><?= $species->count ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
{{-- //$this->include('pagination') --}}
    @include('partials/download-link')

    @include('partials/pagination')
<?php
}
else
{ ?>
	<?php if (! empty($nameSearchString)) { ?>
	<div class="alert alert-warning" role="alert">
		No records could be found matching those criteria.
	</div>
	<?php } ?>
<?php } ?>


</x-layout>
