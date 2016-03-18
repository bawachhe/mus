<?php
$c = mysqli_connect('localhost', 'root');

mysqli_select_db($c, 'ampache');

$sql = "select * from artist order by name asc";

$q = mysqli_query($c, $sql) or die("Can't fetch!");

header('Content-Type: text/html;charset=UTF-8');

while ($row = mysqli_fetch_array($q)) {

	?><a <?
		?>class="solo" <?
		?>href="#ar<?= $row['id'] ?>" <?
		?>onClick="getStuff('albums_songs', 'artist', <?= $row['id'] ?>);"<?
	?>><?=

		htmlentities(
			(
				strlen($row['prefix']) > 0 ?
					$row['prefix'] . " "
				:
					""
			) . $row['name'],
			ENT_QUOTES,
			'UTF-8'
		)

	?></a><?

}

mysqli_close($c);
?>
