#!/bin/bash
# this script can download, tag, and archive music

# paths to stuff
ARCHIVES="archives"
SONGS="songs"
ARTWORK="artwork"

# set arguments to variables
TITLE="$(echo "$1" | tr -d '\' | tr -s ' ' | sed 's|\ *$||')"
TAG_TITLE="$2"
ARTIST="$(echo "$3" | tr -d '\' | tr -s ' ')"
ALBUM="$(echo "$4" | tr -d '\' | tr -s ' ')"
IMG="$5"
TRACK_NUMBER="$6"
if [ "$TRACK_NUMBER" != "false" ]; then
	TRACK_NUMBER_SPACE="$TRACK_NUMBER. "
fi
TOTAL_TRACKS="$7"
URL="$8"
MIX_TITLE="$(echo "$9" | cut -d/ -f3)"
RECURSIVE="${10}"
DOWNLOAD_ID="${11}"
SONG_ID="${12}"

# paths to more stuff
SONG_SAVE="$SONGS/$SONG_ID"
ZIP_SAVE="$MIX_TITLE.zip"
ZIP_DIR="$MIX_TITLE/$DOWNLOAD_ID/"

if [ "$(echo "$IMG" | grep -E "(images)")" ]; then
	ARTWORK_SAVE="$ARTWORK/$SONG_ID.jpeg"
else
	ARTWORK_SAVE="$ARTWORK/$MIX_TITLE.png"
fi

SAVE_TITLE="${TRACK_NUMBER_SPACE}$(echo "$TITLE" | sed 's|/|-|g;s|^\.||g' | tr -d '\#')"

[ "$TAG_TITLE" == "false" ] && unset TITLE
[ "$ARTIST" == "false" ] && unset ARTIST
[ "$ALBUM" == "false" ] && unset ALBUM
[ "$IMG" == "false" ] && unset IMG
[ "$TRACK_NUMBER" == "false" ] && unset TRACK_NUMBER TOTAL_TRACKS
[ "$RECURSIVE" == "false" ] && unset RECURSIVE

while [ -f "SONG_SAVE".part ]; do
	# if the song is being downloaded by someone else

	sleep 2
done

if [ -f "$SONG_SAVE".m4a ]; then
	# if m4a exists

	EXT=".m4a"
	touch "$SONG_SAVE$EXT"
elif [ -f "$SONG_SAVE".mp3 ]; then
	# if mp3 exists

	EXT=".mp3"
	touch "$SONG_SAVE$EXT"
else
	# if song doesn't exist

	curl -Lso "$SONG_SAVE".part "$URL"

	if [ "$(file "$SONG_SAVE".part | grep -i -E "(MPEG ADTS|Audio file with ID3)")" ]; then
    EXT=".mp3"
  elif [ "$(file "$SONG_SAVE".part | grep -i "MPEG v4")" ]; then
    EXT=".m4a"
  elif [ "$(file "$SONG_SAVE".part | grep -i "WAVE audio")" ]; then
    EXT=".wav"
  elif [ "$(file "$SONG_SAVE".part | grep -i "Adaptive Multi-Rate")" ]; then
    EXT=".amr"
  elif [ "$(file "$SONG_SAVE".part | grep -i "Microsoft ASF")" ]; then
    EXT=".wma"
  elif [ "$(file "$SONG_SAVE".part | grep -i "AIFF")" ]; then
    EXT=".aif"
  elif [ "$(file "$SONG_SAVE".part | grep -i "AIFF-C")" ]; then
    EXT=".aifc"
  elif [ "$(file "$SONG_SAVE".part | grep -i "FLAC")" ]; then
    EXT=".flac"
  elif [ "$(file "$SONG_SAVE".part | grep -i "Ogg")" ]; then
    EXT=".ogg"
  elif [ "$(file "$SONG_SAVE".part | grep -i "layer II,")" ]; then
    EXT=".mp2"
  else
    EXT=".txt"
    echo "Unable to download: $URL. Sorry ):" > "$SONG_SAVE".part
  fi

	mv "$SONG_SAVE".part "$SONG_SAVE$EXT"
fi

# tag artwork
if [ "$EXT" == ".mp3" ]; then
	./eyeD3 --remove-images -t "$TITLE" -a "$ARTIST" -A "$ALBUM" -n "$TRACK_NUMBER" -N "$TOTAL_TRACKS" "$SONG_SAVE$EXT" &> /dev/null
	if [ -n "$IMG" ]; then
		[ ! -f "$ARTWORK_SAVE" ] && curl -Lso "$ARTWORK_SAVE" "$IMG"
		./eyeD3 --add-image="$ARTWORK_SAVE":FRONT_COVER "$SONG_SAVE$EXT" &> /dev/null
	fi
elif [ "$EXT" == ".m4a" ]; then
	[ -n "$TOTAL_TRACKS" ] && TOTAL_TRACKS="/$TOTAL_TRACKS"
	./AtomicParsley "$SONG_SAVE$EXT" -o "$SONG_SAVE.temp" --title "$TITLE" --artist "$ARTIST" --album "$ALBUM" --tracknum "$TRACK_NUMBER$TOTAL_TRACKS" --artwork "REMOVE_ALL" &> /dev/null
	if [ -n "$IMG" ]; then
		[ ! -f "$ARTWORK_SAVE" ] && curl -Lso "$ARTWORK_SAVE" "$IMG"
		./AtomicParsley "$SONG_SAVE.temp" -o "$SONG_SAVE$EXT" --artwork "$ARTWORK_SAVE" &> /dev/null
		rm -f "$SONG_SAVE.temp"
	else
		mv "$SONG_SAVE.temp" "$SONG_SAVE$EXT"
	fi
fi

if [ -n "$RECURSIVE" ]; then
	# if we are adding to zip

	mkdir -p "$ARCHIVES/$ZIP_DIR"
	cp "$SONG_SAVE$EXT" "$ARCHIVES/$ZIP_DIR$SAVE_TITLE$EXT"
	printf "$ARCHIVES/$ZIP_DIR$ZIP_SAVE\n$MIX_TITLE.zip\n$EXT"
else
	# if we are downloading single file

	printf "$SONG_SAVE$EXT\n$SAVE_TITLE$EXT\n$EXT"
fi
