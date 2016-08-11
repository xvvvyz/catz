const base = require('./webpack.config.base.js');
const LiveReloadPlugin = require('webpack-livereload-plugin');
const sassLintPlugin = require('sasslint-webpack-plugin');

module.exports = base.merge({
  debug: true,
  devtool: 'cheap-module-eval-source-map',
  plugins: [
    new LiveReloadPlugin({ appendScriptTag: true }),
    new sassLintPlugin({ context: ['./app/scss/'] })
  ]
});
