<?php

require 'inc.bootstrap.php';

$client->ensureLogin();
$books = $client->getCatalogue();
$collections = $client->getCollections($books, $skipCollections);

include 'tpl.header.php';

?>

<style>
div.table {
	width: 100%;
	overflow: auto;
}
tr.filter-hide {
	display: none;
}
td.rating-1-5 {
	background: red;
}
td.rating-2-5 {
	background: orange;
}
td.rating-3-5 {
	background: yellow;
}
td.rating-4-5 {
	background: lime;
}
td.rating-5-5 {
	background: green;
	color: white;
}
</style>

<h1><span id="filter-showing"><?= count($books) ?></span> / <?= count($books) ?> books</h1>

<p><select id="filter-collection"><?= html_options($collections, null, '-- All') ?></select></p>

<div class="table">
<table border="1" cellspacing="0" cellpadding="6">
	<thead>
		<tr>
			<th>Author</th>
			<th>Title</th>
			<th>Entry date</th>
			<th>Rating</th>
			<th>Collections</th>
		</tr>
	</thead>
	<tbody>
		<? foreach ($books as $book): ?>
			<tr data-collections="<?= html(json_encode($book->getCollections())) ?>">
				<td><?= html($book->author) ?></td>
				<td><?= html($book->title) ?></td>
				<td><?= html($book->entry_date) ?></td>
				<td class="rating-<?= $book->rating ?>-5"><?= $book->rating ? $book->rating . ' / 5' : '' ?></td>
				<td><?= implode(', ', $book->getCollections($skipCollections)) ?></td>
			</tr>
		<? endforeach ?>
	</tbody>
</table>
</div>

<script>
var rows = document.querySelectorAll('tr[data-collections]');
var filterShowingElement = document.querySelector('#filter-showing');
var filterCollectionElement = document.querySelector('#filter-collection');

for (var i = 0; i < rows.length; i++) {
	rows[i]._collections = JSON.parse(rows[i].dataset.collections);
}

function filter() {
	var value = filterCollectionElement.value;
	var count = 0;
	for (var i = 0; i < rows.length; i++) {
		var hide = value && rows[i]._collections.indexOf(value) == -1;
		rows[i].classList[hide ? 'add' : 'remove']('filter-hide');
		count += hide ? 0 : 1;
	}

	filterShowingElement.textContent = count;
}

filterCollectionElement.value && filter();

filterCollectionElement.onchange = function(e) {
	filter();
};
</script>

<?php

include 'tpl.footer.php';
