<html>

<head>

<meta charset="UTF-8" />
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
<link rel="shortcut icon" href="favicon.png" />

<title>mUSE</title>

<style type="text/css">
html, body {
	background: linear-gradient(to bottom, #111, #111 80%, #222);
	background-color: #000;
	background-image: url(/bg.jpg);
	background-repeat: no-repeat;
	background-size: cover;
	color: #DDD;
	font-family: "Open Sans", sans;
	font-size: 14px;
	margin: 0;
	min-height: 100vh;
	overflow: hidden;
	padding: 0;
}

a, a:active, a:focus {
	color: #2AC;
	text-decoration: none;
}

a:hover {
	color: #3DF;
}

button {
	background-color: rgba(0,0,0,0.8);
	border: 1px solid #333;
	border-radius: 4px;
	color: #DDD;
}

header, div, input#artistFilter {
	border-radius: 4px;
	overflow-x: hidden;
	position: absolute;
	text-overflow: ellipsis;
	white-space: nowrap;
}

div a.solo {
	display: block;
}

header, #browser, #artistFilter, #artists, #albums_songs, #queueControls, #queue {
	background-color: rgba(0,0,0,0.7);
}

header {
	height: 14vh;
	left: 0.5vw;
	text-align: center;
	top: 1vh;
	width: 99vw;
}

audio {
	height: 50%;
	outline: none;
	width: 95%;
}

#browser {
	height: 84vh;
	left: 0.5vw;
	top: 16vh;
	width: 9.5vw;
}

#artistFilter {
	border: none;
	color: #FFF;
	height: 5.5vh;
	left: 10.5vw;
	top: 16vh;
	width: 19.5vw;
}

#artists {
	height: 78vh;
	left: 10.5vw;
	top: 22vh;
	width: 19.5vw;
}

#albums_songs {
	font-size: 12px;
	height: 84vh;
	left: 30.5vw;
	top: 16vh;
	width: 49.5vw;
}

#albums_songs div {
	position: relative;
}

#songInfo {
	font-size: 20px;
	margin: 10px 0;
	width: 100%;
}

#songInfo .artist-title-song {
	display: block;
}

#songInfo .album-song {
	color: #AAA;
	display: block;
	font-size: 16px;
}

.dl, .play {
	float: right;
}

.album {
	border: 1px solid #333;
	float: left;
	width: 34%;
}

.album img {
	height: 100px;
	width: 100px;
}

.songs {
	border: 1px solid #333;
	float: right;
	overflow: hidden;
	width: 64%;
}

.songs span {
	display: block;
	line-height: 160%;
}

.songs span.track {
	display: inline-block;
	text-align: right;
	width: 20px;
}

.songs span a {
	display: inline;
}

.clear {
	display: block;
	float: none;
	height: 20px;
	width: 100%;
}

#queueControls {
	height: 3.5vh;
	left: 80.5vw;
	top: 16vh;
	width: 19vw;
}

#queue {
	font-size: 12px;
	height: 80vh;
	left: 80.5vw;
	top: 20vh;
	width: 19vw;
}

#queue span {
	clear: both;
	display: block;
	height: 1.5em;
	position: relative;
	width: 100%;
}

#queue span.playing {
	background-color: rgba(255,255,255,0.4);
}

#queue span .label {
	left: 5px;
	overflow: hidden;
	position: absolute;
	right: 10px;
	text-overflow: ellipsis;
	white-space: nowrap;
}

#queue span .del {
	position: absolute;
	right: 2px;
}
</style>

</head>

<body onLoad="onLoad()">
	<header>
		<audio controls></audio>

		<div id="songInfo"></div>
	</header>
	
	<div id="browser"></div>

	<input id="artistFilter" onChange="filterArtistList(this.value)" onKeyUp="filterArtistList(this.value)" placeholder="filter regex..." type="text" />

	<div id="artists"></div>

	<div id="albums_songs"></div>

	<div id="queueControls">
		<button onClick="clearQueue();">Clear Queue</button>
		<input id="repeat" type="checkbox" value="rpt" /> Repeat All
	</div>

	<div id="queue"></div>
</body>

<script type="text/javascript" src="./sortable.js"></script>

<script type="text/javascript">
	var artistArray = [];

	var currentSong = null;

	var filterRe;

	var player = document.getElementsByTagName('audio')[0];

	var queue = document.getElementById("queue");

	var songInfo = document.getElementById("songInfo");

	var sortableObj = null;

	function onLoad() {
		getStuff("artists");

		permalinkLoad();

		player.addEventListener('ended', queueNext);
		player.addEventListener('pause', configTitle);
		player.addEventListener('play', configTitle);
	}

	function clearPlayer() {
		player.pause();
		player.src = '';
		songInfo.innerHTML = '';
		currentSong = null;
		document.title = "mUSE";
	}

	function clearQueue() {
		queue.innerHTML = '';
		clearPlayer();
	}

	function configTitle() {
		var currentSongInfo = "";

		if (currentSong != null) {
			currentSongInfo = " " +  currentSong.innerHTML + " - " + currentSong.getAttribute("artist") + " ::";
		}

	 	if (player.paused) {
			if (document.title.indexOf("▶") > -1) {
				document.title = "mUSE";
			}
		}
		else {
			if (document.title.indexOf("▶" < 0)) {
				document.title = "▶ " + currentSongInfo + " mUSE";
			}
		}
	}

	function doFilter(a) {
		return a && (a.innerHTML.toLowerCase().match(filterRe));
	}

	function filterArtistList(val) {
		var artistsNode = document.getElementById("artists");

		if (!val || val=="") {
			artistsNode.innerHTML = "";

			for (var i in arr) {
				artistsNode.appendChild(artistArray);
			}
		}

		filterRe = new RegExp(val, 'gi');

		var arr = artistArray.filter(doFilter);

		artistsNode.innerHTML = "";

		for (var i in arr) {
			artistsNode.appendChild(arr[i]);
		}
	}

	function getStuff(table, where, id, ai) {
		var params = "w=" + where + "&i=" + id;

		if (ai) {
			params += "&ai=" + ai;
		}

		var x = new XMLHttpRequest();

		x.onreadystatechange = function() {
			if (x.readyState == 4 && x.status == 200) {
				document.getElementById(table).innerHTML = x.responseText;

				if (table == "artists") {
					loadArtistArray();
				}
			}
		};

		x.open("GET", table + ".php?" + params, true);

		x.send();
	}

	function loadArtistArray() {
		var artistsNode = document.getElementById("artists");

		artistArray = [].slice.call(artistsNode.childNodes);
	}

	function permalinkLoad() {
		if (window.location.hash) {
			var type = window.location.hash.substr(1,2);

			var id = window.location.hash.substr(3);

			if (type == "ar") {
				getStuff('albums_songs', 'artist', id);
			}
			else if (type == "al") {
				getStuff('albums_songs', 'album', id);
			}
			else if (type == "so") {
				getStuff('albums_songs', 'song', id);
			}
		}
	}

	function playSong(a) {
		player.src = a.getAttribute("href");

		var songSpan = document.createElement("span");
		songSpan.classList.add("artist-title-song");
		songSpan.innerHTML = a.getAttribute("artist") + " - " + a.innerHTML;

		var albumSpan = document.createElement("span");
		albumSpan.classList.add("album-song");
		albumSpan.innerHTML = a.getAttribute("album");

		songInfo.innerHTML = '';
		songInfo.appendChild(songSpan);
		songInfo.appendChild(albumSpan);

		currentSong = a;

		var queuedSongs = queue.querySelectorAll("span");

		for (var i=0;i<queuedSongs.length;i++) {
			queuedSongs[i].classList.remove("playing");
		}

		a.parentElement.classList.add("playing");

		player.play();
	}

	function queueAlbum(elem) {
		var songsDiv = elem.nextSibling;

		var songs = songsDiv.querySelectorAll(".song");

		for (var i=0;i<songs.length;i++) {
			songs[i].click();
		}
	}

	function queueNext() {
		var repeat = document.getElementById("repeat");

		if (currentSong != null) {
			if (currentSong.parentElement.nextSibling != null) {
				playSong(currentSong.parentElement.nextSibling.firstChild);
			}
			else if (repeat.checked) {
				if (queue.firstChild) {
					playSong(queue.firstChild.firstChild);
				}
			}
		}
	}

	function queueSong(url, label, artist, album) {
		if (sortableObj != null) {
			sortableObj.destroy();
			sortableObj = null;
		}

		var song = document.createElement("span");
		var a = document.createElement("a");
		a.classList.add("label");
		a.setAttribute("href", url);
		a.setAttribute("onClick", "playSong(this); return false;");
		a.innerHTML = label;
		a.setAttribute("artist", artist);
		a.setAttribute("album", album);

		var del = document.createElement("a");
		del.classList.add("del");
		del.setAttribute("href", "#");
		del.setAttribute("onClick", "removeSong(this); return false;");
		del.innerHTML = "X";

		song.appendChild(a);
		song.appendChild(del);

		if (!queue.firstChild) {
			playSong(a);
		}

		queue.appendChild(song);

		sortableObj = Sortable.create(queue);
	}

	function removeSong(delElement) {
		var song = delElement.parentElement;

		var queue = document.getElementById("queue");

		if ((currentSong != null) && (currentSong == delElement.previousSibling)) {
			if (player.playing) {
				if (delElement.parentElement.nextSibling) {
					playSong(delElement.parentElement.nextSibling.firstChild);
				}
				else if (delElement.parentElement.previousSibling) {
					playSong(delElement.parentElement.previousSibling.firstChild);
				}
				else {
					clearPlayer();
				}
			}
			else {
				clearPlayer();
			}
		}

		queue.removeChild(song);
	}
</script>

</html>
