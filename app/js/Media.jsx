import React from 'react';
import url from 'url';
import MediaHeader from 'MediaHeader.jsx';
import EightTracks from 'EightTracks.jsx';
import SoundCloud from 'SoundCloud.jsx';
import Cat from 'Cat.jsx';

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
