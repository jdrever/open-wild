<?php
$lastSlashInUrl= strripos(url()->current(), "/")+1;
$urlToLastSlash=substr(url()->current(),0, $lastSlashInUrl)
?>
<nav>
		<ul class="pagination justify-content-center">
	<?php
	$range = 3;
	if ($results->currentPage + $range >7) : ?>
			<li class="page-item"><a class="page-link" onclick="updateDataset(1); }}); return false;" href="{{ url()->current() . '?page=' . '1' }}">First</a></li>
	<?php endif ?>
	<?php if ($results->currentPage > 1) : ?>
			<li class="page-item"><a class="page-link" onclick="updateDataset({{ ($results->currentPage - 1) }}); return false;" href="{{ url()->current() . '?page=' . ($results->currentPage - 1) }}">Previous</a></li>
	<?php endif ?>
	<?php
		for ($x = ($results->currentPage - $range); $x < (($results->currentPage + $range) + 1); $x++)
		{
			if (($x > 0) && ($x <= $results->numberOfPages))
			{
				if ($x == $results->currentPage)
				{
					?>

			<li class="page-item"><span class="page-link" style="font-weight:bold;"><?= $x?></span></li>
		<?php
				}
				else
				{
					?>
 			<li class="page-item"><a class="page-link" onclick="updateDataset({{ $x }}); return false;" href="{{ url()->current() . '?page=' . $x }}"><?= $x?></a></li>
		<?php
			}
		}
	} ?>
	<?php
	if ($results->currentPage<$results->numberOfPages) : ?>
			<li class="page-item"><a class="page-link" onclick="updateDataset({{ ($results->currentPage + 1) }}); return false;"  href="{{ url()->current() . '?page=' . ($results->currentPage+1) }}">Next</a></li>
	<?php endif ?>
	<?php
	if (($results->currentPage + $range)<$results->numberOfPages) : ?>
			<li class="page-item"><a class="page-link" onclick="updateDataset({{ ($results->numberOfPages - 1) }}); return false;"  href="{{ url()->current() . '?page=' . ($results->numberOfPages) }} ">Last</a></li>
	<?php endif ?>
	</ul>
	<p class="text-center" style="font-size:small;"><?= $results->numberOfRecords ?> records in <?= $results->numberOfPages ?> pages </p>
</nav>
