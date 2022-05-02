<?php if (isset($results->records)&&count($results->records)>0) { ?>
    <table class="table mt-3">
		<thead>
			<tr>
				<th>Site</th>
				<th>Record Count</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($results->records as $site) : ?>
				<tr>
					<td>
						<a href="{{ '/site/' . $site->label . '/type/' . $speciesNameType . '/group/' . $speciesGroup .  '/axiophytes/' . $axiophyteFilter }}">
							<?= $site->label ?>
						</a>
					</td>
					<td><?= $site->count ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
    @include('partials/download-link')

    @include('partials/pagination')

    <?php
}
else
{ ?>
	<?php if ($showResults) { ?>
	<div class="alert alert-warning" role="alert">
		No records could be found matching those criteria.
	</div>
	<?php } ?>
<?php } ?>

<?php if (isset($results->queryUrl)) { ?>
    <details style="font-size:small;"><summary>NBN API Query</summary>{{ $results->queryUrl }}</details>
<?php } ?>
