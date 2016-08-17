const path = require('path');
const _ = require('lodash');
const fs = require('fs');

const externals = {
  'cheerio': 'window',
  'react/lib/ExecutionEnvironment': true,
  'react/lib/ReactContext': true,
};

fs.readdirSync('node_modules')
  .filter(mod => ['.bin'].indexOf(mod) === -1)
  .forEach(mod => externals[mod] = 'commonjs ' + mod);

const defaults = {
  target: 'electron-renderer',
  entry: ['babel-polyfill', './app/js/app.jsx'],
  output: {
    path: './app/bundle',
    filename: 'app.js'
  },
  externals: externals,
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
  }
}

module.exports.defaults = defaults;

module.exports.merge = function merge(config) {
  return _.merge({}, defaults, config);
};
