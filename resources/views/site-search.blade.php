<x-layout>

<form action="/" action="post">
    @csrf
    <div class="row mb-2">
        <div class="col-lg-8 mx-auto">
            <label for="search" class="form-label visually-hidden">Site name</label>
            <div class="input-group">
                <input type="text" class="form-control" name="search" id="search" aria-describedby="search-help" placeholder="Enter a site" value="{{ $siteName }}" />
                <button type="submit" class="btn btn-primary">List Sites</button>
            </div>
            <small id="search-help" class="form-text text-start text-md-center d-block">Enter all or part of a site name. Try something like "Aston".</small>
        </div>
    </div>
</form>

<?php if (isset($sites) && count($sites) > 0) : ?>
	<table class="table mt-3">
		<thead>
			<tr>
				<th>Site</th>
				<th>Record Count</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($sites as $site) : ?>
				<tr>
					<td>
						<a href="<?= base_url('/site/' . $site->label . '/group/' . $speciesGroup .  '/type/' . $nameType . '/axiophyte/' . $axiophyteFilter); ?>">
							<?= $site->label ?>
						</a>
					</td>
					<td><?= $site->count ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>

    @include('partials/pagination')

    @include('partials/download-link')

<?php elseif (!empty($siteSearchString)) : ?>
	<div class="alert alert-warning" role="alert">
		No records could be found matching those criteria.
	</div>
<?php endif ?>





</x-layout>

