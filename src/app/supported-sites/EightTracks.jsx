import React from 'react';
import MediaInfo from 'MediaInfo.jsx';
import Song from 'Song.jsx';
import Timer from 'Timer.jsx';
import request from 'request';

const MAX_INITIAL_SONGS = 15;
const REQUEST_PADDING = 1000;

export default class EightTracks extends React.Component {
  constructor() {
    super();

    this.token = Math.floor(Math.random()
      * 100000000000000000) + 1000000000;

    this.state = {
      invalid: false,
      playlist: {},
      songs: [],
      timeout: REQUEST_PADDING,
      timer: false,
    };

    this.getInitialSongs = this.getInitialSongs.bind(this);
    this.getNextSong = this.getNextSong.bind(this);
  }

  componentDidMount() {
    this.getPlaylistInfo().then(this.getInitialSongs);
  }

  getPlaylistInfo() {
    return new Promise((resolve) => {
      request({
        url: `${this.props.url}?format=jsonh`,
        json: true,
      }, (error, res, body) => {
        if (res.statusCode === 200) {
          if (body.mix) this.setState({ playlist: body.mix });
          else this.setState({ invalid: true });

          this.nextSongUrl = `https://8tracks.com/sets/${this.token}/next?mix_id=` +
            `${body.mix.id}&format=jsonh`;
        } else {
          this.setState({ invalid: true });
        }

        resolve();
      });
    });
  }

  getInitialSongs() {
    (async () => {
      const songs = await new Promise(resolve => {
        let songs = [];
        let i = 0;

        while (i < this.state.playlist.tracks_count) {
          request({
            url: this.nextSongUrl,
            json: true,
          }, (error, res, body) => {
            const success = res.statusCode === 200 && body.set.track.name;
            if (success) songs.push(body.set.track);
            const last = songs.length === this.state.playlist.tracks_count;
            if (!success || last) resolve(songs);
          });

          i++;
        }
      });

      this.setState({ songs: songs });
      if (songs.length < this.state.playlist.tracks_count) this.songFailure();
    })();
  }

  getNextSong() {
    request({
      url: this.nextSongUrl,
      json: true,
    }, (error, res, body) => {
      const success = res.statusCode === 200 && body.set.track.name;
      if (success) this.songSuccess(body.set.track);
      else if (this.consecutiveFails < 1) this.songFailure();
      else this.setState({ timer: false, timeout: REQUEST_PADDING});
    });
  }

  songSuccess(song) {
    this.consecutiveFails = 0;

    this.setState({
      timer: false,
      timeout: 0,
      songs: [...this.state.songs, song],
    });

    if (this.state.songs.length < this.state.playlist.tracks_count) {
      setTimeout(this.getNextSong, this.state.timeout);
    }
  }

  songFailure() {
    this.consecutiveFails++;

    const audio = new Audio(this.state.songs
      .slice(-1)[0].track_file_stream_url);

    audio.addEventListener('canplaythrough', () => {
      this.setState({ timer: true, timeout: audio.duration * 1000 });
      setTimeout(this.getNextSong, this.state.timeout);
    });
  }

  renderSongs() {
    return this.state.songs.map((song, key) => {
      return <Song
        key={key}
        title={song.name}
        artist={song.performer}
        album={this.state.playlist.name}
        artwork={this.state.playlist.cover_urls.static_cropped_imgix_url}
        artworkThumb={this.state.playlist.cover_urls.sq72}
        url={song.track_file_stream_url}
        playlistName={this.state.playlist.name}
        trackNum={key + 1}
        totalTracks={this.state.playlist.tracks_count}
      />
    });
  }

  render() {
    return (
      <div>
        <MediaInfo
          type="8tracks"
          invalid={this.state.invalid}
          title={this.state.playlist.name || this.props.url}
        />
        {this.renderSongs()}
        {this.state.timer && <Timer ms={this.state.timeout} />}
      </div>
    )
  }
};
