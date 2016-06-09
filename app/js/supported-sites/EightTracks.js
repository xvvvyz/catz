import React from 'react';
import MediaHeader from 'MediaHeader';
import Song from 'Song';
const request = window.require('request');

class EightTracks extends React.Component {
  constructor() {
    super();

    this.token = Math.floor(Math.random() * 100000000000000000000) + 1000000000000000;

    this.state = {
      invalid: false,
      playlist: {},
      songs: []
    };
  }

  componentDidMount() {
    const url = `${this.props.url}?format=jsonh`;

    request(url, (error, res, body) => {
      if (res.statusCode === 200) {
        const json = JSON.parse(body.toString());

        if (typeof json.mix === 'object') {
          this.setState({playlist: json.mix});
          this.getSong();
        } else {
          this.setState({invalid: true});
        }
      } else {
        this.setState({invalid: true});
      }
    });
  }

  getSong() {
    const url = `https://8tracks.com/sets/${this.token}/next?mix_id=${this.state.playlist.id}&format=jsonh`;

    request(url, (error, res, body) => {
      if (res.statusCode === 200) {
        const json = JSON.parse(body.toString());
        this.setState({songs: [...this.state.songs, json.set.track]});
        this.getSong();
      }
    });
  }

  render() {
    const songs = this.state.songs.map((song, key) => {
      return <Song
        key={key}
        title={song.name}
        artist={song.performer}
        album={this.state.playlist.name}
        artwork={this.state.playlist.cover_urls.static_cropped_imgix_url}
        artwork_thumb={this.state.playlist.cover_urls.sq72}
        url={song.track_file_stream_url}
        playlist={(this.state.playlist.name)}
        playlist_name={this.state.playlist.name}
        track_num={key + 1}
      />
    });

    return (
      <div>
        <MediaHeader type="8tracks" invalid={this.state.invalid} title={this.props.url} />
        {songs}
      </div>
    )
  }
}

export default EightTracks;
