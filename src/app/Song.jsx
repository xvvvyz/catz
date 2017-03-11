import React from 'react';
import os from 'os';
import request from 'request';
import progress from 'request-progress';
import readChunk from 'read-chunk';
import fileType from 'file-type';
import path from 'path';
import fileExists from 'file-exists';
import fs from 'fs';
import md5 from 'md5';
import nodeID3 from 'node-id3';
import open from 'open';
import sanitize from 'sanitize-filename';
import leftPad from 'left-pad';
import 'Song.css';

export default class Song extends React.Component {
  constructor() {
    super();

    this.state = { percentage: 0, done: false };

    this.showSong = this.showSong.bind(this);
  }

  componentDidMount() {
    (async () => {
      this.tmpSong = await this.downloadSong();
      this.tmpArt = await this.downloadArtwork();
      this.downloadDir = await this.getDownloadDir();
      this.downloadFile = this.getDownloadFile();
      await this.tagSong();
      await this.moveSong();

      this.setState({ done: true });
    })();
  }

  downloadSong() {
    return new Promise((resolve, reject) => {
      const path = `${os.tmpdir()}/${md5(this.props.url)}`;

      progress(request(this.props.url))
        .on('progress', p => this.setState({ percentage: p.percent * 100 }))
        .on('end', () => {
          this.setState({ percentage: 100 });
          resolve(path);
        }).pipe(fs.createWriteStream(path));
    });
  }

  downloadArtwork() {
    return new Promise((resolve, reject) => {
      const path = `${os.tmpdir()}/${md5(this.props.artwork)}`;

      if (!fileExists(path)) {
        request(this.props.artwork, () => resolve(path))
          .pipe(fs.createWriteStream(path));
      } else {
        resolve(path);
      }
    });
  }

  getDownloadDir() {
    return new Promise(resolve => {
      let dir = path.join(os.homedir(), 'Downloads');

      if (this.props.playlistName) {
        dir = path.join(dir, this.sanitizePath(this.props.playlistName));
      }

      fs.mkdir(dir, () => resolve(dir));
    });
  }

  getDownloadFile() {
    let name = this.sanitizePath(this.props.title);
    let ext = this.getExtention(this.tmpSong);
    let filename = `${name}.${ext}`;

    if (this.props.playlistName) {
      filename = `${leftPad(this.props.trackNum, 2, 0)} ${filename}`;
    }

    return path.join(this.downloadDir, filename);
  }

  sanitizePath(path) {
    return sanitize(path.trim(), { replacement: '-' });
  }

  tagSong() {
    switch (this.getExtention(this.tmpSong)) {
      case 'mp3': this.tagMp3(); break;
      case 'm4a': this.tagM4a(); break;
    }
  }

  verifyImage(image) {
    return this.getExtention(image).match(/(jpg|png)/);
  }

  getExtention(file) {
    let { ext } = fileType(readChunk.sync(file, 0, 262)) || { ext: 'txt' };

    switch (ext) {
      case 'mp4': return 'm4a';
      default: return ext;
    }
  }

  tagMp3() {
    const tags = {
      title: this.props.title,
      artist: this.props.artist,
      album: this.props.album,
      image: this.verifyImage(this.tmpArt) ? this.tmpArt : false,
      trackNumber: `${this.props.trackNum}/${this.props.totalTracks}`,
    };

    nodeID3.removeTags(this.tmpSong);
    nodeID3.write(tags, this.tmpSong);
  }

  tagM4a() {
    // TODO
  }

  moveSong() {
    return new Promise(resolve => {
      fs.rename(this.tmpSong, this.downloadFile, () => resolve());
    });
  }

  showSong() {
    const osx = process.platform === 'darwin';
    const windows = process.platform === 'win32';

    if (osx || windows) open(this.downloadFile, 'desktop');
    else open(this.downloadDir);
  }

  render() {
    const progress = { width: `${this.state.percentage}%` }

    return (
      <div className="song">
        <div className="song__progress" style={progress} data-toggled={!this.state.done} />
        <img className="song__artwork" src={this.props.artworkThumb} />
        {this.props.totalTracks && <span className="song__num">{this.props.trackNum}</span>}
        <span className="song__title">{this.props.title}</span>
        <span className="song__artist">{this.props.artist}</span>
        <button className="song__show" onClick={this.showSong} disabled={!this.state.done} data-toggled={this.state.done}>Show</button>
      </div>
    );
  }
};
