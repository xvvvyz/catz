import React from 'react';
import ReactDOM from 'react-dom';
import _ from 'lodash';
import Media from 'Media.jsx';
import 'Main.css';

export default class Main extends React.Component {
  constructor() {
    super();

    this.state = { things: [] };
  }

  handlePaste(event) {
    const pasted = event.clipboardData.getData('text/plain');

    if (_.includes(this.state.things, pasted)) {
      const elRef = _.indexOf(this.state.things, pasted);
      const el = ReactDOM.findDOMNode(this.refs[elRef]);

      // TODO: make it obvious that they pasted a dup...
    } else {
      this.setState({ things: [...this.state.things, pasted] });
    }
  }

  componentDidMount() {
    window.addEventListener('paste', this.handlePaste.bind(this));
  }

  renderThings() {
    return this.state.things.map((thing, key) => {
      return (
        <div className="media" key={key}>
          <Media ref={key} thing={thing} />
        </div>
      );
    });
  }

  render() {
    const things = this.renderThings();

    return (
      <div className={`god-wrapper platform-${process.platform}`}>
        {things}
        <p className="instructions" data-toggled={!things.length}>
          Paste a link&hellip;
        </p>
      </div>
    );
  }
};
