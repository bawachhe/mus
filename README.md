I'm a fan of the [Ampache project](https://github.com/ampache/ampache) but I wanted a basic interface to it that felt a little more like the music players of today in its visual organization.

This... thing... is what resulted.  It's good, if basic.  You can search music by artist, see album-song listings of artists, send links to your friends, and create basic playlists.  It uses a basic HTML5 audio player to play your songs from a linked folder.

You can't save the playlists or search by anything other than artists.

Things you need to do other than copy this somewhere and point a webserver to it:

1. Have Ampache set up with a loaded music library ([Ampache's website](http://ampache.com) will help you)
2. Link the root of your music folder into your mus folder i.e. `ln -s /path/to/MusicDir /path/to/mus/site/root/Music` \*
3. Link the local Ampache root into your mus folder i.e. `ln -s /path/to/ampache /path/to/mus/site/root/amp` \*

\* If you want to change that functionality, be my guest.  The associated code is all in [albums_songs.php](https://github.com/bawachhe/mus/tree/master/albums_songs.php).
