import React from 'react';
import MediaHeader from 'MediaHeader';
import EightTracks from 'EightTracks';
import SoundCloud from 'SoundCloud';
import Cat from 'Cat';
import url from 'url';

export default class Media extends React.Component {
  componentDidMount() {
    const god =  document.querySelector('.god-wrapper');
    god.scrollTop = god.scrollHeight;
  }

  render() {
    const thing = this.props.thing;

    if (thing === 'cat') return <Cat />;

    switch (url.parse(thing).hostname) {
      case '8tracks.com': return <EightTracks url={thing} />;
      case 'soundcloud.com': return <SoundCloud url={thing} />;
      default: return <MediaHeader type="Nonsense" invalid={true} title={thing} />;
    }
  }
};
