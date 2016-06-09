import React from 'react';
import MediaHeader from 'MediaHeader';
import Song from 'Song';
const request = window.require('request');

class SoundCloud extends React.Component {
  constructor() {
    super();

    this.state = {
      invalid: false,
      client_id: '02gUJC0hH2ct1EGOcYXQIzRFU91c72Ea',
      playlist: {},
      songs: []
    };
  }

  componentDidMount() {
    const url = `https://api.soundcloud.com/resolve.json?url=${encodeURIComponent(this.props.url)}&limit=1&client_id=${this.state.client_id}`;

    request(url, (error, res, body) => {
      if (res.statusCode === 200) {
        const json = JSON.parse(body.toString());

        switch (json.kind) {
          case 'track':
            this.setState({songs: [json]});
            break;
          case 'playlist':
            const tracks = json.tracks;
            delete json.tracks;
            this.setState({playlist: json, songs: tracks});
            break;
          default:
            switch (json[0].kind) {
              case 'track':
                this.setState({songs: json});
                break;
              default:
                this.setState({invalid: true});
            }
        }
      } else {
        this.setState({invalid: true});
      }
    });
  }

  render() {
    const songs = this.state.songs.map((song, key) => {
      return <Song
        key={key}
        title={song.title}
        artist={song.user.username}
        album=''
        artwork={song.artwork_url}
        artwork_thumb={song.artwork_url}
        url={`${song.stream_url}?client_id=${this.state.client_id}`}
        playlist={(this.state.playlist.title)}
        playlist_name={this.state.playlist.title}
        track_num={key + 1}
      />
    });

    return (
      <div>
        <MediaHeader type="SoundCloud" invalid={this.state.invalid} title={this.props.url} />
        {songs}
      </div>
    );
  }
}

export default SoundCloud;
