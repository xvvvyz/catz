import React from 'react';
import MediaInfo from 'MediaInfo.jsx';
import Song from 'Song.jsx';
import request from 'request';

const CLIENT_ID = '2t9loNQH90kzJcsFCODdigxfp325aq4z';

export default class SoundCloud extends React.Component {
  constructor() {
    super();

    this.state = { invalid: false, playlist: {}, songs: [] };
  }

  componentDidMount() {
    (async () => {
      try {
        this.resolveJson(await this.resolveUrl());
      } catch(err) {
        this.setState({ invalid: true });
      }
    })();
  }

  resolveUrl() {
    return new Promise((resolve, reject) => {
      const url = `https://api.soundcloud.com/resolve.json?url=` +
        `${encodeURIComponent(this.props.url)}` +
        `&limit=1&client_id=${CLIENT_ID}`;

      request({ url: url, json: true }, (error, res, body) => {
        if (res.statusCode === 200) resolve(body);
        else reject();
      });
    });
  }

  resolveJson(json) {
    switch (json.kind) {
      case 'track':
        this.setState({
          songs: [json]
        });

        break;

      case 'playlist':
        this.setState({
          playlistTitle: json.title,
          playlistTrackCount: json.track_count,
          playlistAuthor: json.user.username,
          songs: json.tracks,
        });

        break;

      default:
        this.setState({
          invalid: true
        });
    }
  }

  renderSongs() {
    return this.state.songs.map((song, key) => {
      return <Song
        key={key}
        title={song.title}
        artist={song.user.username || this.state.playlistAuthor}
        album={this.state.playlistTitle || ''}
        artwork={song.artwork_url ? song.artwork_url.replace('large', 't500x500') : ''}
        artworkThumb={song.artwork_url}
        url={`${song.stream_url}?client_id=${CLIENT_ID}`}
        playlistName={this.state.playlistTitle}
        trackNum={this.state.playlistTrackCount ? key + 1 : 1}
        totalTracks={this.state.playlistTrackCount || 1}
      />
    });
  }

  render() {
    return (
      <div>
        <MediaInfo
          type="SoundCloud"
          invalid={this.state.invalid}
          title={this.state.playlist.title || this.props.url}
        />
        {this.renderSongs()}
      </div>
    );
  }
};
