import React from 'react';
import ReactDOM from 'react-dom';
import _ from 'lodash';
import Media from 'Media';
import 'main.scss';

class Main extends React.Component {
  constructor() {
    super();
    this.state = {things: []};
  }

  handlePaste(event) {
    const pasted = event.clipboardData.getData('text/plain');

    if (_.includes(this.state.things, pasted)) {
      const elRef = _.indexOf(this.state.things, pasted);
      const el = ReactDOM.findDOMNode(this.refs[elRef]);
      // TODO: make it obvious that they pasted a dup...
    } else {
      this.setState({things: [...this.state.things, pasted]});
    }
  }

  handleKeypress(event) {
    if (event.which === 13) {
      this.setState({things: [...this.state.things, 'cat']});
    }
  }

  componentDidMount() {
    window.addEventListener('paste', this.handlePaste.bind(this));
    window.addEventListener('keypress', this.handleKeypress.bind(this));
  }

  render() {
    const things = this.state.things.map((thing, key) => <Media key={key} ref={key} thing={thing} />);
    return <div className="main">{things}</div>;
  }
}

export default Main;
