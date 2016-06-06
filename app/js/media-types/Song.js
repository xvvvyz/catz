import React from 'react';
const os = window.require('os');
const request = window.require('request');
const progress = window.require('request-progress');
const readChunk = window.require('read-chunk');
const fileType = window.require('file-type');
const tmp = window.require('temporary');
const path = window.require('path');
const fs = window.require('fs');
import 'song.scss';

class Song extends React.Component {
  constructor() {
    super();
    this.state = {percentage: 0};
  }

  download() {
    const tmpFile = new tmp.File().path;
    this.setState({download_disabled: true});

    progress(request(this.props.url)).on('progress', state => {
      this.setState({percentage: state.percentage * 100});
    }).on('error', (error) => {
      console.log(error);
      this.setState({download_disabled: false});
    }).on('end', () => {
      this.setState({percentage: 100});
      let {ext} = fileType(readChunk.sync(tmpFile, 0, 262));
      ext = ext === 'mp4' ? 'm4a' : ext;

      switch (ext) {
        case 'mp3':
        case 'm4a':
          // TODO: tag the shit
      }

      const file = path.join(os.homedir(), 'Downloads', `${this.props.title}.${ext}`);
      fs.rename(tmpFile, file);
    }).pipe(fs.createWriteStream(tmpFile));
  }

  render() {
    const progress = {width: `${this.state.percentage}%`}

    return (
      <div className="song">
        <div className="song__progress" style={progress}></div>
        <img className="song__artwork" src={this.props.artwork_thumb} />
        <span className="song__title">{this.props.title}</span>
        <span className="song__artist">{this.props.artist}</span>
        <button className="song__download" onClick={this.download.bind(this)} disabled={this.state.download_disabled}>Download</button>
      </div>
    );
  }
}

export default Song;
