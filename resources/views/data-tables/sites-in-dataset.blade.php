@if ($showResults)
    @if (isset($results->records)&&count($results->records)>0)
        @include('partials/download-link')

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
						<a href="{{ '/site/' . $site->name . '/type/' . $speciesNameType . '/group/' . $speciesGroup .  '/axiophytes/' . $axiophyteFilter }}">
							<?= $site->name ?>
						</a>
					</td>
					<td><?= $site->recordCount ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
        @include('partials/download-link')

        @include('partials/pagination')
    @else
        @include('partials/no-records')
    @endif
    @include('partials/nbn-query')
@endif



