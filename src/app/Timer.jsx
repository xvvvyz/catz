import React from 'react';
import prettySeconds from 'pretty-seconds';
import 'Timer.css';

export default class Timer extends React.Component {
  constructor() {
    super();
    this.state = { seconds: 0 };
  }

  componentDidMount() {
    this.setState({ seconds: Math.ceil(this.props.ms / 1000) });

    this.interval = setInterval(() => {
      this.setState({ seconds: this.state.seconds - 1 });
      this.state.seconds === 0 && clearInterval(this.interval);
    }, 1000);
  }

  componentWillUnmount() {
    clearInterval(this.interval);
  }

  render() {
    return (
      <div className="timer">
        waiting {prettySeconds(this.state.seconds)}&hellip;
      </div>
    );
  }
};
