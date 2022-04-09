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
