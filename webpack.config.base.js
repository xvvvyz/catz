const path = require('path');
const _ = require('lodash');

const defaults = {
  target: 'electron-renderer',
  entry: './app/js/app.jsx',
  output: {
    path: './app/bundle',
    filename: 'app.js'
  },
  module: {
    loaders: [
      {
        test: /\.jsx$/,
        loader: 'babel',
        exclude: /node_modules/,
        query: { presets: ['react', 'es2015', 'stage-0'] }
      },
      {
        test: /\.scss$/,
        loaders: ['style', 'css', 'sass']
      }
    ]
  },
  resolve: {
    root: [
      path.resolve('app'),
      path.resolve('app/scss'),
      path.resolve('app/js'),
      path.resolve('app/js/supported-sites'),
      path.resolve('app/js/media-types')
    ]
  }
}

module.exports.defaults = defaults;

module.exports.merge = function merge(config) {
  return _.merge({}, defaults, config);
};
