import React from 'react';
import 'cat.scss';

class Cat extends React.Component {
  constructor() {
    super();

    const cacheBust = new Date().getTime();
    this.cat = `http://thecatapi.com/api/images/get?format=src&time=${cacheBust}`;
  }

  render() {
    const cat = {backgroundImage: `url(${this.cat})`};
    return <div className='cat' style={cat}></div>;
  }
}

export default Cat;
