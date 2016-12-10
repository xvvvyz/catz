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
import ID3Writer from 'browser-id3-writer';
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
      const tmpSong = await this.downloadSong();
      const tmpArt = await this.downloadArtwork();

      await this.nameSong(tmpSong);
      await this.tagSong(tmpSong, tmpArt);
      await this.moveSong(tmpSong);
      this.setState({ done: true });
    })();
  }

  downloadSong() {
    return new Promise((resolve, reject) => {
      const tmpSong = `${os.tmpdir()}/${md5(this.props.url)}`;

      progress(request(this.props.url))
        .on('progress', p => this.setState({ percentage: p.percent * 100 }))
        .on('end', () => {
          this.setState({ percentage: 100 });
          resolve(tmpSong);
        }).pipe(fs.createWriteStream(tmpSong));
    });
  }

  downloadArtwork() {
    return new Promise((resolve, reject) => {
      let tmpArt = `${os.tmpdir()}/${md5(this.props.artwork)}`;

      if (!fileExists(tmpArt)) {
        request(this.props.artwork, () => resolve(tmpArt))
          .pipe(fs.createWriteStream(tmpArt));
      } else {
        resolve(tmpArt);
      }
    });
  }

  nameSong(tmpSong) {
    this.downloadDir = path.join(os.homedir(), 'Downloads');

    let filename = (
      `${this.sanitizePath(this.props.title)}.${this.getExtention(tmpSong)}`
    );

    if (this.props.playlistName) {
      this.downloadDir = path.join(
        this.downloadDir,
        this.sanitizePath(this.props.playlistName)
      );

      filename = `${leftPad(this.props.trackNum, 2, 0)} ${filename}`;
    }

    this.filePath = path.join(this.downloadDir, filename);
  }

  sanitizePath(path) {
    return sanitize(path.trim(), { replacement: '-' });
  }

  tagSong(tmpSong, tmpArt) {
    return new Promise((resolve, reject) => {
      const songBuffer = fs.readFileSync(tmpSong);
      const tags = this.getTags(tmpArt);
      let tagged = false;

      switch (this.getExtention(tmpSong)) {
        case 'mp3': tagged = this.tagMp3(songBuffer, tags); break;
        case 'm4a': tagged = this.tagM4a(songBuffer, tags); break;
      }

      if (tagged) fs.writeFile(tmpSong, tagged, resolve);
      else resolve();
    });
  }

  getTags(tmpArt) {
    return {
      title: this.props.title,
      artist: this.props.artist,
      album: this.props.album,
      cover: this.verifyImage(tmpArt) ? fs.readFileSync(tmpArt) : false,
      trackNum: this.props.trackNum,
      totalTracks: this.props.totalTracks
    };
  }

  verifyImage(image) {
    return this.getExtention(image).match(/(jpg|png)/);
  }

  getExtention(file) {
    let { ext } = fileType(readChunk.sync(file, 0, 262)) || { ext: 'txt' };
    return ext === 'mp4' ? 'm4a' : ext;
  }

  tagMp3(songBuffer, tags) {
    const writer = new ID3Writer(songBuffer);

    writer.setFrame('TIT2', tags.title);
    writer.setFrame('TPE1', [tags.artist]);
    writer.setFrame('TALB', tags.album);
    writer.setFrame('TRCK', `${tags.trackNum}/${tags.totalTracks}`);
    tags.cover && writer.setFrame('APIC', tags.cover);
    writer.addTag();

    return new Buffer(writer.arrayBuffer);
  }

  tagM4a(songBuffer, tags) {
    // TODO: find or build an m4a tagger
    return false;
  }

  moveSong(tmpSong) {
    return new Promise(resolve => {
      fs.mkdir(this.downloadDir, () => {
        fs.rename(tmpSong, this.filePath, () => resolve());
      });
    });
  }

  showSong() {
    const osx = process.platform === 'darwin';
    const windows = process.platform === 'win32';

    if (osx || windows) open(this.filePath, 'desktop');
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
