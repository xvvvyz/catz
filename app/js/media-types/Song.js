import React from 'react';
const os = window.require('os');
const request = window.require('request');
const progress = window.require('request-progress');
const readChunk = window.require('read-chunk');
const fileType = window.require('file-type');
const tmp = window.require('tmp');
const path = window.require('path');
const fs = window.require('fs');
const open = window.require('open');

const urlify = window.require('urlify').create({
  toLower: true,
  trim: true,
  spaces: '-',
  nonPrintable: '_'
});

import 'song.scss';

class Song extends React.Component {
  constructor() {
    super();
    this.state = {
      percentage: 0
    };
  }

  componentDidMount() {
    this.download();
  }

  download() {
    const tmpFile = tmp.fileSync().name;
    this.setState({downloadDisabled: true});

    progress(request(this.props.url)).on('progress', state => {
      this.setState({
        percentage: state.percentage * 100
      });
    }).on('error', (error) => {
      this.setState({
        downloadDisabled: false
      });

      console.log(error);
    }).on('end', () => {
      this.setState({
        percentage: 100
      });

      let {ext} = fileType(readChunk.sync(tmpFile, 0, 262));
      ext = ext === 'mp4' ? 'm4a' : ext;

      let downloadPath = path.join(os.homedir(), 'Downloads');
      let filePath = `${this.props.title.trim().replace(/[/\\]/g, '-')}.${ext}`;

      if (this.props.playlist) {
        downloadPath = path.join(downloadPath, urlify(this.props.playlist_name));
        filePath = `${this.props.track_num}. ${filePath}`;
      }

      // TODO: tag metadata...

      filePath = path.join(downloadPath, filePath);
      fs.mkdir(downloadPath, () => fs.rename(tmpFile, filePath));

      this.openDir = downloadPath;
      this.openFile = filePath;
    }).pipe(fs.createWriteStream(tmpFile));
  }

  show() {
    if (process.platform === 'darwin' || process.platform === 'win32') {
      open(this.openFile, 'desktop');
    } else {
      open(this.openDir);
    }
  }

  render() {
    const progress = {width: `${this.state.percentage}%`}
    const done = this.state.percentage === 100;

    return (
      <div className="song">
        <div className="song__progress" style={progress} data-toggled={!done}></div>
        <img className="song__artwork" src={this.props.artwork_thumb} />
        <span className="song__title">{this.props.title}</span>
        <span className="song__artist">{this.props.artist}</span>
        <button className="song__show" onClick={this.show.bind(this)} disabled={!done} data-toggled={done}>Show</button>
      </div>
    );
  }
}

export default Song;
