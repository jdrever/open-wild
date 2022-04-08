<nav>
		<ul class="pagination justify-content-center">
	<?php
	$range = 3;
	if ($page + $range >7) : ?>
			<li class="page-item"><a class="page-link" href="<?= url()->current() . '?page=1' ?>">First</a></li>
	<?php endif ?>
	<?php if ($page > 1) : ?>
			<li class="page-item"><a class="page-link" href="<?= url()->current() . '?page=' . ($page - 1) ?>">Previous</a></li>
	<?php endif ?>
	<?php
		for ($x = ($page - $range); $x < (($page + $range) + 1); $x++)
		{
			if (($x > 0) && ($x <= $totalPages))
			{
				if ($x === $page)
				{
					?>

			<li class="page-item"><span class="page-link" style="font-color:#000000;"><?= $x?></span></li>
		<?php
				}
				else
				{
					?>
 			<li class="page-item"><a class="page-link" href="<?= url()->current() . '?page=' . $x?> "><?= $x?></a></li>
		<?php
			}
		}
	} ?>
	<?php
	if ($page<$totalPages) : ?>
			<li class="page-item"><a class="page-link" href="<?= url()->current() . '?page=' . ($page+1) ?>">Next</a></li>
	<?php endif ?>
	<?php
	if (($page + $range)<$totalPages) : ?>
			<li class="page-item"><a class="page-link" href="<?= url()->current() . '?page=' .$totalPages?>">Last</a></li>
	<?php endif ?>
	</ul>
	<p class="text-center" style="font-size:small;"><?= $totalRecords ?> records in <?= $totalPages ?> pages </p>
</nav>
