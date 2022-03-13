<x-layout>

<h2 class="text-start text-md-center">Search for a Species in Shropshire</h2>

{{-- form_open('species') --}}
<form method="post" action="/" accept-charset="UTF-8">
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
			<input class="form-check-input" type="radio" name="speciesNameType" id="scientific-name" value="scientific" onchange="if (this.form.search.value!='') { this.form.submit(); }" {{-- set_radio('name-type', 'scientific', ($nameType === 'scientific')); --}} />
			<label class="form-check-label" for="scientific-name">
				scientific<span class="d-none d-lg-inline"> name only</span>
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="speciesNameType" id="common-name" value="common"  onchange="if (this.form.search.value!='') { this.form.submit(); }" {{-- set_radio('name-type', 'common', ($nameType === 'common')); --}} />
			<label class="form-check-label" for="common-name">
				common<span class="d-none d-lg-inline"> name only</span>
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="checkbox" name="axiophyte-filter" id="axiophyte-filter" value="true"  onchange="if (this.form.search.value!='') { this.form.submit(); }" {{-- set_radio('axiophyte-filter', 'true', ($axiophyteFilter === 'true')); --}} />
			<label class="form-check-label" for="axiophyte-name">
				<span class="d-lg-none">axiophytes</span>
				<span class="d-none d-lg-inline">axiophytes only</span>
			</label>
		</div>
	</div>
	<div class="form-group col-sm-4 col-lg-3">
		<div class="form-check">
			<input class="form-check-input" type="radio" name="species-group" id="plants" value="plants"  onchange="if (this.form.search.value!='') { this.form.submit(); }" {{-- set_radio('groups', 'plants', $speciesGroup === 'plants'); --}} />
			<label class="form-check-label" for="plants">
				only plants
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="species-group" id="bryophytes" value="bryophytes"  onchange="if (this.form.search.value!='') { this.form.submit(); }"{{--  set_radio('groups', 'bryophytes', $speciesGroup === 'bryophytes'); --}} />
			<label class="form-check-label" for="bryophytes">
				only bryophytes
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="species-group" id="both" value="both"  onchange="if (this.form.search.value!='') { this.form.submit(); }" {{-- set_radio('groups', 'both', $speciesGroup === 'both'); --}} />
			<label class="form-check-label" for="both">
				both <span class="d-none d-xl-inline">plants and bryophytes</span>
			</label>
		</div>
	</div>
</div>
{{-- //form_close() --}}
</form>

<?php if (isset($message)) { ?>
	<div class="alert alert-danger" role="alert">
		I am very sorry, but an error has occured.</b>:  <?= $message ?>
	</div>
<?php } ?>

<!-- Show the search results if there are any -->
<?php if (isset($records)&&count($records)>0) { ?>
	<?php if (isset($downloadLink)) { ?>
	<p><a href="<?= $downloadLink ?>">Download this data</a></p>
	<?php } ?>
	<table class="table mt-3">
		<thead>
			<tr>
				<th class="d-none d-md-table-cell">Family</th>
				<th <?php if ($nameType === 'common') { ?>class="d-none d-sm-table-cell" <?php } ?>>Scientific Name</th>
				<th <?php if ($nameType === 'scientific') { ?>class="d-none d-sm-table-cell" <?php } ?>>Common Name</th>
				<th>Records</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($records as $species) { ?>
				<?php $speciesArray = explode('|', (string)$species->label); ?>
				<tr>
				<?php if ($nameType === 'scientific') { ?>
					<td class="d-none d-md-table-cell"><?= $speciesArray[4] ?></td>
					<td><a href="<?= '/species/' . $speciesArray[0] . '?nameSearchString=' . $nameSearchString ?>"><?= $speciesArray[0] ?></a></td>
					<td class="d-none d-sm-table-cell">
						<a href="<?= '/species/' . $speciesArray[0] . '?displayName=' . $speciesArray[2] . '&nameSearchString=' . $nameSearchString ?>"><?= $speciesArray[2] ?></a>
					</td>
				<?php } ?>
				<?php if ($nameType === 'common') { ?>
					<td class="d-none d-md-table-cell"><?= $speciesArray[5] ?></td>
					<td class="d-none d-sm-table-cell"><a href="<?= '/species/' . $speciesArray[1] . '?nameSearchString=' . $nameSearchString ?>"><?= $speciesArray[1] ?></a></td>
					<td>
						<a href="<?= urldecode('/species/' . $speciesArray[1] . '?displayName=' . $speciesArray[0] . '&nameSearchString=' . $nameSearchString) ?>"><?= $speciesArray[0] ?></a>
					</td>
				<?php } ?>
					<td><?= $species->count ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
{{-- //$this->include('pagination') --}}

	<?php if (isset($downloadLink)) { ?>
		<p><a href="<?= $downloadLink ?>">Download this data</a></p>
	<?php } ?>
<?php }
else
{ ?>
	<?php if (! empty($nameSearchString)) { ?>
	<div class="alert alert-warning" role="alert">
		No records could be found matching those criteria.
	</div>
	<?php } ?>
<?php } ?>


</x-layout>
