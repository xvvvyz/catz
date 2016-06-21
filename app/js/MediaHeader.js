import React from 'react';
import 'media-header.scss';

// for css class names...
const camelCaseToDashed = (str) => {
  return str.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
}

const MediaHeader = (props) => {
  return (
    <div className="media-header">
      {props.invalid ? <span className={`media-header__tag invalid`}>Invalid</span> : ''}
      <span className={`media-header__tag type-${camelCaseToDashed(props.type)}`}>{props.type}</span>
      <span className="media-header__title">{props.title}</span>
    </div>
  );
};

export default MediaHeader;
