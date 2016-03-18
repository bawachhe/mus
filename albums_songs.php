<?php
$c = mysqli_connect('localhost', 'root');

mysqli_select_db($c, 'ampache');

$wheres = [ "artist", "album", "song" ];

$where = "";

if (isset($_GET['w']) && in_array($_GET['w'], $wheres)) {
	$where = $_GET['w'];
}

$id = 0;

if (isset($_GET['i']) && is_numeric($_GET['i'])) {
	$id = $_GET['i'];
}

$artistId = 0;

if (isset($_GET['ai']) && is_numeric($_GET['ai'])) {
	$artistId = $_GET['ai'];
}

$sql = "select ";

$sql .= "song.id as songId, ";
$sql .= "song.track as songTrack, ";
$sql .= "song.title as songTitle, ";
$sql .= "song.file as songFile, ";
$sql .= "song.album as albumId, ";
$sql .= "song.artist as artistId, ";

$sql .= "album.prefix as albumPrefix, ";
$sql .= "album.name as albumName, ";
$sql .= "album.year as albumYear, ";

$sql .= "artist.prefix as artistPrefix, ";
$sql .= "artist.name as artistName ";

$sql .= "from song ";

$sql .= "inner join artist ";
$sql .= "on song.artist = artist.id ";

$sql .= "left join album ";
$sql .= "on song.album = album.id";

if ($where == "song") {
	$sql .= " where song.id = $id";
} elseif ($where != "" && $id > 0) {
	$sql .= " where $where = $id";

	if ($where == "album" && $artistId > 0) {
		$sql .= " and artist = $artistId";
	}
}

$sql .= " order by ";
$sql .= "album.name asc, ";
$sql .= "song.track asc, ";
$sql .= "song.title asc, ";
$sql .= "artist.name asc";

header('Content-Type: text/html;charset=UTF-8');

$q = mysqli_query($c, $sql) or die("Can't fetch! " . mysqli_error());

$curAlbum = -1;

while ($row = mysqli_fetch_array($q)) {
	if ($curAlbum == -1) {

		?><div><?
			?><h2><?	
				?><a <?
					?>href="#ar<?= $row['artistId'] ?>" <?
					?>onClick="getStuff('albums_songs', 'artist', <?= $row['artistId'] ?>);"><?

					?><?=
						htmlentities(
							(
								strlen($row['artistPrefix']) > 0 ?
									$row['artistPrefix'] . " "
								:
									""
							) . $row['artistName'],
							ENT_QUOTES, 'UTF-8'
						)

				?></a><?

				?> ( <a href="/amp/batch.php?action=artist&id=<?= $row['artistId'] ?>">&#8681;</a> ) <?

				if ($where == "album" || $where == "song") {
					?> :: <?

					?><a <?
						?>href="#al<?= $row['albumId'] ?>" <?
						?>onClick="getStuff('albums_songs', 'album', <?= $row['albumId'] ?>, <?= $row['artistId'] ?>);"><?

						?><?= 
							htmlentities(
								(
									strlen($row['albumPrefix']) > 0 ?
										$row['albumPrefix'] . " "
									:
										""
								) . $row['albumName'],
								ENT_QUOTES, 'UTF-8'
							)

					?></a><?

					if ($where == "song") {

						?> :: <?
						?><a <?
							?>href="#so<?= $row['songId'] ?>" <?
							?>onClick="getStuff('albums_songs', 'song', <?= $row['songId'] ?>);"><?

							?><?= htmlentities($row['songTitle'], ENT_QUOTES, 'UTF-8') ?><?
						?></a><?

					}

				}
			?></h2><?
		?></div><?

		$curAlbum = 0;
	}

	if ($curAlbum != $row['albumId']) {
		$curAlbum = $row['albumId'];

		if ($curAlbum != 0) {
			?></div><?
			?><div class="clear">&nbsp;</div><?
		}

		$qI = mysqli_query($c, "select * from image where object_type = 'album' and object_id = " . $row['albumId']) or die("albuminfo fail " . mysqli_error());

		$rowI = mysqli_fetch_array($qI);

		?><div class="album"><?

			if (isset($rowI['image'])) {
				?><img src="data:image/jpeg;base64,<?= base64_encode( $rowI['image'] ) ?>" /><br /><?
			}

			?><a class="play" href="#al<?= $row['albumId'] ?>" onClick="queueAlbum(this.parentElement)">Play &#9654;</a><?

			?><a <?
				?>href="#al<?= $row['albumId'] ?>" <?
				?>onClick="getStuff('albums_songs', 'album', <?= $row['albumId'] ?>, <?= $row['artistId'] ?>);"><?

				?><?=
					htmlentities(
						(
							strlen($row['albumPrefix']) > 0 ?
								$row['albumPrefix'] . " "
							:
								""
						) . $row['albumName'],
						ENT_QUOTES, 'UTF-8'
					)

			?></a><br /><?

			?><a class="dl" href="/amp/batch.php?action=album&id[0]=<?= $row['albumId'] ?>">Download &#8681;</a><?

			?><?= $row['albumYear'] ?><?
		?></div><?
		?><div class="songs"><?

	}

	?><span><?
		?><a class="dl" href="/amp/stream.php?action=download&song_id=<?= $row['songId'] ?>">Download &#8681;</a><?

		?><span class="track"><?
			?><?= $row['songTrack']=="0" ? "" : $row['songTrack'] ?><?
		?></span> <?

		?><a class="song" href="#so<?= $row['songId'] ?>" <?
			?>onClick="queueSong('<?
				?><?=
					str_replace(
						array("'", '"'),
						array("%27", "%22"),
						substr(
							$row['songFile'],
							9
						)
					)
			?>','<?=
				htmlentities(
					htmlentities(
						$row['songTitle'],
						ENT_QUOTES, 'UTF-8'
					), ENT_QUOTES, 'UTF-8'
				)

			?>','<?=
				htmlentities(
					htmlentities(
						(
							strlen($row['artistPrefix']) > 0 ?
								$row['artistPrefix'] . " "
							:
								""
						) . $row['artistName'],
						ENT_QUOTES, 'UTF-8'
					), ENT_QUOTES, 'UTF-8'
				)

			?>','<?=
				htmlentities(
					htmlentities(
						(
							strlen($row['albumPrefix']) > 0 ?
								$row['albumPrefix'] . " "
							:
								""
						) . $row['albumName'],
						ENT_QUOTES, 'UTF-8'
					), ENT_QUOTES, 'UTF-8'
				)

		?>');<?
		?>"><?

			?><?=
				htmlentities(
					$row['songTitle'],
					ENT_QUOTES, 'UTF-8'
				)

		?></a><?
	?></span><?
}

?></div>

<? mysqli_close($c); ?>
