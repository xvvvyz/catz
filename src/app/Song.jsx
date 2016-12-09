import React from 'react';
import os from 'os';
import request from 'request';
import progress from 'request-progress';
import readChunk from 'read-chunk';
import fileType from 'file-type';
import osTmpdir from 'os-tmpdir';
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
    this.showStuff = this.showStuff.bind(this);
  }

  componentDidMount() {
    (async () => {
      const tmpSong = await this.downloadSong();
      const tmpArtwork = await this.downloadArtwork();

      await this.nameStuff(tmpSong);
      await this.tagStuff(tmpSong, tmpArtwork);
      await this.moveStuff(tmpSong);
      this.setState({ done: true });
    })();
  }

  downloadSong() {
    return new Promise((resolve, reject) => {
      const tmpSong = `${osTmpdir()}/${md5(this.props.url)}`;

      progress(request(this.props.url))
        .on('progress', p => this.setState({ percentage: p.percent * 100 }))
        .on('error', error => reject(error))
        .on('end', () => {
          this.setState({ percentage: 100 });
          resolve(tmpSong);
        }).pipe(fs.createWriteStream(tmpSong));
    });
  }

  downloadArtwork() {
    return new Promise((resolve, reject) => {
      const tmpArtwork = `${osTmpdir()}/${md5(this.props.artwork)}`;

      if (!fileExists(tmpArtwork)) {
        request(this.props.artwork, () => resolve(tmpArtwork))
          .pipe(fs.createWriteStream(tmpArtwork));
      } else {
        resolve();
      }
    });
  }

  nameStuff(tmpSong) {
    this.dir = path.join(os.homedir(), 'Downloads');
    this.file = `${this.props.title.trim().replace(/[/\\]/g, '-')}` +
      `.${this.getExtention(tmpSong)}`;

    if (this.props.playlistName) {
      this.dir = path.join(this.dir, this.props.playlistName);
      this.file = `${leftPad(this.props.trackNum, 2, 0)} ${this.file}`;
    }
  }

  tagStuff(tmpSong, tmpArtwork) {
    return new Promise((resolve, reject) => {
      const songBuffer = fs.readFileSync(tmpSong);

      const tags = {
        title: this.props.title,
        artist: this.props.artist,
        album: this.props.album,
        cover: tmpArtwork ? fs.readFileSync(tmpArtwork) : null,
        trackNum: this.props.trackNum,
        totalTracks: this.props.totalTracks
      }

      let tagged = false;

      switch (this.getExtention(tmpSong)) {
        case 'mp3': tagged = this.tagMp3(songBuffer, tags); break;
        case 'm4a': tagged = this.tagM4a(songBuffer, tags); break;
      }

      if (tagged) fs.writeFile(tmpSong, tagged, resolve);
      else resolve();
    });
  }

  tagMp3(songBuffer, tags) {
    const writer = new ID3Writer(songBuffer);

    writer.setFrame('TIT2', tags.title)
      .setFrame('TPE1', [tags.artist])
      .setFrame('TALB', tags.album)
      .setFrame('APIC', tags.cover)
      .setFrame('TRCK', `${tags.trackNum}/${tags.totalTracks}`)
      .addTag();

    return new Buffer(writer.arrayBuffer);
  }

  tagM4a(songBuffer, tags) {
    // TODO: find or build an m4a tagger
    return false;
  }

  moveStuff(tmpSong) {
    return new Promise(resolve => {
      this.file = path.join(this.dir, sanitize(this.file));

      fs.mkdir(this.dir, () => {
        fs.rename(tmpSong, this.file, () => resolve());
      });
    });
  }

  showStuff() {
    const osx = process.platform === 'darwin';
    const windows = process.platform === 'win32';

    if (osx || windows) open(this.file, 'desktop');
    else open(this.dir);
  }

  getExtention(tmpSong) {
    let {ext} = fileType(readChunk.sync(tmpSong, 0, 262));
    return ext === 'mp4' ? 'm4a' : ext;
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
        <button className="song__show" onClick={this.showStuff} disabled={!this.state.done} data-toggled={this.state.done}>Show</button>
      </div>
    );
  }
};
