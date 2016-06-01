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
      songs: []
    };
  }

  componentDidMount() {
    this.getSong();
  }

  getSong() {
    const url = `https://api.soundcloud.com/resolve.json?url=${encodeURIComponent(this.props.url)}&client_id=${this.state.client_id}`;

    request(url, (error, res, body) => {
      if (res.statusCode === 200) {
        const json = JSON.parse(body.toString());

        if (json.kind === 'track') {
          this.setState({songs: [...this.state.songs, json]});
        } else {
          this.setState({invalid: true});
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
        url={`${song.stream_url}&client_id=${this.state.client_id}`}
      />
    });

    return (
      <div>
        <MediaHeader type='SoundCloud' invalid={this.state.invalid} title={this.props.url} />
        {songs}
      </div>
    );
  }
}

export default SoundCloud;
