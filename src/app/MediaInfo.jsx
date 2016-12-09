import React from 'react';
import 'MediaInfo.css';

export default function MediaInfo(props) {
  const camelCaseToDashed = (str) => {
    return str.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
  }

  return (
    <div className="media-info">
      {props.invalid ? <span className={`media-info__tag error`}>Invalid</span> : ''}
      <span className={`media-info__tag type-${camelCaseToDashed(props.type)}`}>{props.type}</span>
      <span className="media-info__title">{props.title}</span>
    </div>
  );
};
