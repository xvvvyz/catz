import React from 'react';
import url from 'url';
import MediaInfo from 'MediaInfo.jsx';
import EightTracks from 'supported-sites/EightTracks.jsx';
import SoundCloud from 'supported-sites/SoundCloud.jsx';
import 'Media.css';

export default class Media extends React.Component {
  componentDidMount() {
    const god =  document.querySelector('.god-wrapper');
    god.scrollTop = god.scrollHeight;
  }

  render() {
    const thing = this.props.thing;

    switch (url.parse(thing).hostname) {
      case '8tracks.com': return <EightTracks url={thing} />;
      case 'soundcloud.com': return <SoundCloud url={thing} />;
      default: return <MediaInfo type="Nonsense" invalid={true} title={thing} />;
    }
  }
};
