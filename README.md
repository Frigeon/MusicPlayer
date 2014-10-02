MusicPlayer
===========
This is a basic music player reading a linked directory recursively (including subdirectories), 
in this case the relatively pathed Music directory.

It reads the directories, dumps the heirarchial array structure into a one-dimensional array for a proper full shuffle 
in such a way that you can explode the passed string on : in order to retrieve the relatively pathed location and the base MP3 name.

It then uses a basic HTML5 audio element and some jQuery to allow for playlist control.

Styling is still being worked on, as is basic display methodology.
