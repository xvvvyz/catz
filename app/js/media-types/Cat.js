import React from 'react';
import 'cat.scss';

export default function Cat() {
  const cacheBust = new Date().getTime();
  const cat = `//thecatapi.com/api/images/get?format=src&time=${cacheBust}`;
  const style = { backgroundImage: `url(${cat})` };

  return <div className="cat" style={style}></div>;
};
