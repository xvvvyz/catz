#!/bin/bash

function get_file_type() {
  file_output="$(file "$1")"

  if [ -n "$(echo "$file_output" | grep -i -E "(MPEG ADTS|Audio file with ID3)")" ]; then
    echo "mp3"
  elif [ -n "$(echo "$file_output" | grep -i -E "MPEG v4|MP4 v2")" ]; then
    echo "m4a"
  elif [ -n "$(echo "$file_output" | grep -i "WAVE audio")" ]; then
    echo "wav"
  elif [ -n "$(echo "$file_output" | grep -i "Adaptive Multi-Rate")" ]; then
    echo "amr"
  elif [ -n "$(echo "$file_output" | grep -i "Microsoft ASF")" ]; then
    echo "wma"
  elif [ -n "$(echo "$file_output" | grep -i "AIFF")" ]; then
    echo "aif"
  elif [ -n "$(echo "$file_output" | grep -i "AIFF-C")" ]; then
    echo "aifc"
  elif [ -n "$(echo "$file_output" | grep -i "FLAC")" ]; then
    echo "flac"
  elif [ -n "$(echo "$file_output" | grep -i "Ogg")" ]; then
    echo "ogg"
  elif [ -n "$(echo "$file_output" | grep -i "layer II,")" ]; then
    echo "mp2"
  fi
}

function download_artwork() {
  if [ ! -f "$artwork_save" ]; then
    curl -Lso "$artwork_save" "$artwork_url"
    ./convert "$artwork_save" "$artwork_save"
  fi
}

function atomicparsley_mv() {
  mv "$song_save_server.temp" "$song_save_server"
}


while read -r line; do
  key="$(echo "$line" | grep -o "^[^:]*" | tr -cd "[[:alpha:]]_")"
  value="$(echo "$line" | grep -o "\:.*" | sed "s/^://")"
  [ -z "$key" ] || [ -z "$value" ] && continue
  eval $key="\"$(eval echo $value)\""
done <<< "$(echo -en "$@")"

ARCHIVES="archives"
SONGS="songs"
ARTWORK="artwork"

if [ -n "$mix_artwork" ]; then
  artwork_url="$mix_artwork"
  artwork_save="$ARTWORK/$mix_slug.jpg"
elif [ -n "$song_artwork" ]; then
  artwork_url="$song_artwork"
  artwork_save="$ARTWORK/$song_id.jpg"
fi

if [ -n "$song_number" ]; then
  song_number_fancy="$(printf "%02d" "$song_number"). "
fi

song_name_clean="$(echo "$song_title" | iconv -f utf-8 -t ASCII -c | sed "s|/|:|g")"
song_save_client="$song_number_fancy$song_name_clean"
song_save_server="$SONGS/$song_id"
zip_dir="$ARCHIVES/$mix_slug/$download_id"

if [ -z "$tag_song_title" ]; then
  unset song_title
fi

if [ -f "$song_save_server.m4a" ]; then
  song_ext="m4a"
elif [ -f "$song_save_server.mp3" ]; then
  song_ext="mp3"
else
  curl -Lso "$song_save_server.part" "$song_url"
  song_ext="$(get_file_type "$song_save_server.part")"

  if [ -n "$song_ext" ]; then
    mv "$song_save_server.part" "$song_save_server.$song_ext"
  else
    echo -n "{\"error\": \"1\"}"
  fi
fi

song_save_server="$song_save_server.$song_ext"
song_save_client="$song_save_client.$song_ext"
touch "$song_save_server"

if [ "$song_ext" == "mp3" ]; then
  ./eyeD3 --remove-all "$song_save_server" &> /dev/null

  [ -n "$song_title" ] && t="-t \"$song_title\""
  [ -n "$song_artist" ] && a="-a \"$song_artist\""
  [ -n "$song_album" ] && A="-A \"$song_album\""
  [ -n "$song_title" ] && t="-t \"$song_title\""
  [ -n "$song_number" ] && N="-n $song_number -N $total_songs"
  eval ./eyeD3 $t $a $A $N "$song_save_server" &> /dev/null

  if [ -n "$artwork_save" ]; then
    download_artwork
    ./eyeD3 --add-image="$artwork_save":FRONT_COVER "$song_save_server" &> /dev/null
  fi
elif [ "$song_ext" == "m4a" ]; then
  [ -n "$total_songs" ] && slash_total_songs="/$total_songs"

  ./AtomicParsley "$song_save_server" -o "$song_save_server.temp" --title "$song_title" --artist "$song_artist" --album "$song_album" --tracknum "$song_number$slash_total_songs" --artwork REMOVE_ALL &> /dev/null && atomicparsley_mv

  if [ -n "$artwork_save" ]; then
    download_artwork
    ./AtomicParsley "$song_save_server" -o "$song_save_server.temp" --artwork "$artwork_save" &> /dev/null && atomicparsley_mv
  fi
fi

if [ -n "$recursive" ]; then
  mkdir -p "$zip_dir"
  current_path="$(pwd)"
  ln -s "$current_path/$song_save_server" "$current_path/$zip_dir/$song_save_client"
  echo -n "{\"error\": \"0\"}"
else
  echo -n "{\"error\": \"0\", \"path\": \"$song_save_server\", \"save\": \"$song_save_client\"}"
fi
