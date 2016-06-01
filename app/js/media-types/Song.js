import React from 'react';
import 'song.scss';

class Song extends React.Component {
  render() {
    return (
      <div className='song'>
        <img className='song__artwork' src={this.props.artwork_thumb} width='25' height='25' />
        <span className='song__title'>{this.props.title}</span>
        <span className='song__artist'>{this.props.artist}</span>
      </div>
    );
  }
}

export default Song;
