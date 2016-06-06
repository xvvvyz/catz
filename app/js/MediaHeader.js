import React from 'react';
import 'media-header.scss';

const MediaHeader = (props) => {
  return (
    <div className="media-header">
      {props.invalid ? <span className={`media-header__tag invalid`}>Invalid</span> : ''}
      <span className={`media-header__tag type-${props.type}`}>{props.type}</span>
      <span className="media-header__title">{props.title}</span>
    </div>
  );
};

export default MediaHeader;
